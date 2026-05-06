<?php

namespace App\Http\Controllers;

use App\Events\BidPlaced;
use App\Mail\BidConfirmationMail;
use App\Models\RaOtp;
use App\Models\User;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\BidDistribution;
use App\Models\NpvpConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class RaAuthController extends Controller
{
    public function dashboard()
    {
        $auctions = Auction::whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
            ->where('status', 'in_progress')
            ->with(['npvCategories', 'npvpConfigurations'])
            ->get();

        return view('app.ra.dashboard', compact('auctions'));
    }

    public function auctionPortal(\App\Models\Auction $auction)
    {
        abort_unless(
            $auction->participants()->where('user_id', Auth::id())->exists(),
            403
        );

        abort_if($auction->status !== 'in_progress', 403, 'This auction is not currently in progress.');

        // Redirect to policy page if not yet signed
        $participant = $auction->participants()->where('user_id', Auth::id())->first();
        if (!$participant->sign_policy) {
            return redirect()->route('ra.auction.policy', $auction);
        }

        $auction->load(['npvCategories', 'npvpConfigurations']);

        $highestBid = max(
            (float) str_replace(',', '', $auction->base_price),
            $auction->bids()->where('status', 'confirmed')->max('bid_amount') ?? 0
        );

        $currentNpv = $auction->bids()
            ->where('status', 'confirmed')
            ->orderByDesc('bid_amount')
            ->value('total_npv') ?? 0;

        return view('app.ra.portal', compact('auction', 'highestBid', 'currentNpv'));
    }

    public function showPolicy(\App\Models\Auction $auction)
    {
        abort_unless($auction->participants()->where('user_id', Auth::id())->exists(), 403);
        abort_if($auction->status !== 'in_progress', 403, 'This auction is not currently in progress.');

        $participant = $auction->participants()->where('user_id', Auth::id())->first();
        if ($participant->sign_policy) {
            return redirect()->route('ra.auction.portal', $auction);
        }

        return view('app.ra.policy', compact('auction'));
    }

    public function signPolicy(\App\Models\Auction $auction)
    {
        abort_unless($auction->participants()->where('user_id', Auth::id())->exists(), 403);

        $auction->participants()
            ->where('user_id', Auth::id())
            ->update(['sign_policy' => true, 'sign_policy_at' => now()]);

        return redirect()->route('ra.auction.portal', $auction);
    }

    public function topBids(Auction $auction)
    {
        abort_unless($auction->participants()->where('user_id', Auth::id())->exists(), 403);

        $bids = $auction->bids()
            ->where('status', 'confirmed')
            ->orderByDesc('bid_amount')
            ->limit(10)
            ->get(['bid_amount', 'total_npv', 'created_at']);

        return DataTables::of($bids)
            ->addIndexColumn()
            ->addColumn('bid_amount', fn($b) => '&#8377; ' . number_format($b->bid_amount))
            ->addColumn('total_npv',  fn($b) => '&#8377; ' . number_format($b->total_npv, 2))
            ->addColumn('created_at', fn($b) => $b->created_at->format('d M Y, h:i A'))
            ->rawColumns(['bid_amount', 'total_npv'])
            ->make(true);
    }

    public function myBids(Auction $auction)
    {
        abort_unless($auction->participants()->where('user_id', Auth::id())->exists(), 403);

        $bids = $auction->bids()
            ->where('user_id', Auth::id())
            ->orderBy('created_at')
            ->get();

        return DataTables::of($bids)
            ->addIndexColumn()
            ->addColumn('bid_number', function ($b) use (&$bids) {
                static $i = 0;
                return '#' . (++$i);
            })
            ->addColumn('bid_amount', fn($b) => '&#8377; ' . number_format($b->bid_amount))
            ->addColumn('total_npv',  fn($b) => '&#8377; ' . number_format($b->total_npv, 2))
            ->addColumn('status', function ($b) {
                return match ($b->status) {
                    'confirmed' => '<span class="badge badge-success">Valid</span>',
                    'revision_pending' => '<span class="badge badge-warning">Revision Pending Approval</span>',
                    'revision_rejected' => '<span class="badge badge-danger">Revision Rejected</span>',
                    default => '<span class="badge badge-danger">Invalid</span>',
                };
            })
            ->addColumn('action', function ($b) use ($auction) {
                if (!in_array($b->status, ['confirmed', 'revision_rejected'], true)) {
                    return '<span class="text-muted">—</span>';
                }
                $url = route('ra.auction.bid.revise-data', [$auction, $b]);
                return '<button type="button" class="btn btn-sm btn-outline-primary btn-request-revision" data-url="' . e($url) . '" title="Load this bid in form for editing"><i class="fas fa-edit mr-1"></i>Revise</button>';
            })
            ->addColumn('created_at', fn($b) => $b->created_at->format('d M Y, h:i A'))
            ->rawColumns(['bid_amount', 'total_npv', 'status', 'action'])
            ->make(true);
    }

    public function reviseBidData(Request $request, Auction $auction, AuctionBid $bid)
    {
        abort_unless($auction->participants()->where('user_id', Auth::id())->exists(), 403);
        abort_if($auction->status !== 'in_progress', 403, 'Revision is allowed only while challenge is in progress.');
        abort_if($bid->auction_id !== $auction->id || $bid->user_id !== Auth::id(), 403, 'You can revise only your own bids.');
        abort_if($bid->status === 'revision_pending', 422, 'This bid is already pending approval.');

        $bid->load('distributions');

        return response()->json([
            'bid_id' => $bid->id,
            'bid_amount' => (float) $bid->bid_amount,
            'distributions' => $bid->distributions->map(fn ($d) => [
                'npv_category_id' => (int) $d->npv_category_id,
                'npvp_config_id' => (int) $d->npvp_configuration_id,
                'amount' => (float) $d->amount,
            ])->values(),
        ]);
    }

    public function placeBid(\Illuminate\Http\Request $request, Auction $auction)
    {
        abort_unless($auction->participants()->where('user_id', Auth::id())->exists(), 403);

        abort_if($auction->status !== 'in_progress', 403, 'Bidding is only allowed on in-progress auctions.');

        $request->validate([
            'bid_amount'                         => 'required|numeric|min:0.01',
            'distributions'                      => 'required|array|min:1',
            'distributions.*.npv_category_id'    => 'required|exists:npv_categories,id',
            'distributions.*.npvp_config_id'     => 'required|exists:npvp_configurations,id',
            'distributions.*.amount'             => 'required|numeric|min:0',
            'revise_bid_id'                      => 'nullable|integer|exists:auction_bids,id',
        ]);

        $bidAmount     = (float) $request->bid_amount;
        $distributions = $request->distributions;
        $reviseBidId   = $request->input('revise_bid_id');

        if ($reviseBidId) {
            $sourceBid = AuctionBid::query()
                ->where('id', $reviseBidId)
                ->where('auction_id', $auction->id)
                ->where('user_id', Auth::id())
                ->first();
            if (!$sourceBid) {
                return response()->json(['message' => 'Selected bid for revision was not found.'], 422);
            }
            if ($sourceBid->status === 'revision_pending') {
                return response()->json(['message' => 'Selected bid is already pending approval.'], 422);
            }
        }

        $highestBid = max(
            (float) str_replace(',', '', $auction->base_price),
            $auction->bids()->where('status', 'confirmed')->max('bid_amount') ?? 0
        );

        // Validate based on increment_amount_type and increment_type
        if ($auction->increment_amount_type === 'mandatory') {
            if ($bidAmount <= $highestBid) {
                return response()->json(['message' => 'Bid amount must be greater than current base value (â‚¹' . number_format($highestBid) . ').'], 422);
            }
            $incrementAmount = (float) str_replace(',', '', $auction->increment_amount);
            if ($auction->increment_type === 'fixed') {
                $minBid = $highestBid + $incrementAmount;
                if ($bidAmount < $minBid) {
                    return response()->json(['message' => 'Bid amount must be at least â‚¹' . number_format($minBid) . ' (Current Base + Increment).'], 422);
                }
            } else {
                if ($incrementAmount > 0 && fmod($bidAmount, $incrementAmount) > 0.001) {
                    return response()->json(['message' => 'Bid amount must be a multiple of â‚¹' . number_format($incrementAmount) . '.'], 422);
                }
            }
        }
        // Recommend: no base value or increment enforcement â€” any positive amount is accepted

        // Validate total distributed == bid amount
        $totalDistributed = collect($distributions)->sum(fn($d) => (float) $d['amount']);
        if (round($totalDistributed, 2) !== round($bidAmount, 2)) {
            return response()->json(['message' => 'Total distributed amount must equal the bid amount.'], 422);
        }

        // Calculate total NPV
        $totalNpv = 0;
        foreach ($distributions as $d) {
            $config    = NpvpConfiguration::find($d['npvp_config_id']);
            $totalNpv += (float) $d['amount'] * (float) $config->percentage_value;
        }

        // Compute remark before creating bid
        $prevHighest  = $auction->bids()->where('status', 'confirmed')->max('bid_amount') ?? 0;
        $basePrice    = (float) str_replace(',', '', $auction->base_price);
        $incrementAmt = (float) str_replace(',', '', $auction->increment_amount);
        $runningBase  = max($basePrice, (float) $prevHighest);
        $minBid       = $runningBase + $incrementAmt;

        if ($bidAmount <= $runningBase) {
            $remark = 'BID AMOUNT Less than Base Value';
        } elseif ($auction->increment_amount_type === 'mandatory' && $bidAmount < $minBid) {
            $remark = 'BID AMOUNT does not comply with the requirement of Minimum Incremental Bid Value';
        } else {
            $existingCount = $auction->bids()->count();
            $remark = $existingCount === 0 ? 'INITIAL BASE VALUE' : '-';
        }

        if ($reviseBidId) {
            $bid = AuctionBid::query()
                ->where('id', $reviseBidId)
                ->where('auction_id', $auction->id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $bid->loadMissing('distributions');
            $backup = [
                'bid_amount'        => (float) $bid->bid_amount,
                'total_distributed' => (float) $bid->total_distributed,
                'total_npv'         => (float) $bid->total_npv,
                'status'            => (string) $bid->status,
                'remark'            => $bid->remark,
                'distributions'     => $bid->distributions->map(fn ($d) => [
                    'npv_category_id'       => (int) $d->npv_category_id,
                    'npvp_configuration_id' => (int) $d->npvp_configuration_id,
                    'amount'                => (float) $d->amount,
                    'npv_value'             => (float) $d->npv_value,
                ])->values()->all(),
            ];

            $bid->update([
                'bid_amount'        => $bidAmount,
                'total_distributed' => $totalDistributed,
                'total_npv'         => $totalNpv,
                'status'            => 'revision_pending',
                'ip_address'        => $request->ip(),
                'remark'            => 'REVISION REQUESTED',
                'revision_backup'   => json_encode($backup),
            ]);

            $bid->distributions()->delete();
            foreach ($distributions as $d) {
                $config = NpvpConfiguration::find($d['npvp_config_id']);
                BidDistribution::create([
                    'auction_bid_id'       => $bid->id,
                    'npv_category_id'      => $d['npv_category_id'],
                    'npvp_configuration_id'=> $d['npvp_config_id'],
                    'amount'               => $d['amount'],
                    'npv_value'            => (float) $d['amount'] * (float) $config->percentage_value,
                ]);
            }

            $bid->load(['user', 'auction']);
            event(new BidPlaced($bid));

            return response()->json(['message' => 'Same bid updated and sent for creator approval.', 'bid_id' => $bid->id]);
        }

        $bid = AuctionBid::create([
            'auction_id'        => $auction->id,
            'user_id'           => Auth::id(),
            'bid_amount'        => $bidAmount,
            'total_distributed' => $totalDistributed,
            'total_npv'         => $totalNpv,
            'status'            => 'confirmed',
            'ip_address'        => $request->ip(),
            'remark'            => $remark,
        ]);

        foreach ($distributions as $d) {
            $config = NpvpConfiguration::find($d['npvp_config_id']);
            BidDistribution::create([
                'auction_bid_id'       => $bid->id,
                'npv_category_id'      => $d['npv_category_id'],
                'npvp_configuration_id'=> $d['npvp_config_id'],
                'amount'               => $d['amount'],
                'npv_value'            => (float) $d['amount'] * (float) $config->percentage_value,
            ]);
        }

        $bid->load(['user', 'auction.createdBy', 'auction.npvCategories', 'auction.npvpConfigurations', 'distributions.npvCategory', 'distributions.npvpConfiguration']);

        event(new BidPlaced($bid));

        $bidIndex = $bid->auction->bids()->count();
        \Illuminate\Support\Facades\Mail::to($bid->user->email)
            ->cc(optional($bid->auction->createdBy)->email)
            ->send(new BidConfirmationMail($bid, $bidIndex, $remark));

        return response()->json(['message' => 'Bid placed successfully!', 'bid_id' => $bid->id]);
    }

    public function showLoginForm()
    {
        return view('auth.ra-login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)
            ->where('is_active', 1)
            ->whereHas('roles', fn($q) => $q->where('name', 'ra'))
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No active RA account found with this email.'])->withInput();
        }

        // Invalidate previous OTPs
        RaOtp::where('user_id', $user->id)->where('is_used', false)->update(['is_used' => true]);

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        RaOtp::create([
            'user_id'    => $user->id,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
            'is_used'    => false,
        ]);

        // Send OTP via email
        Mail::raw("Your OTP for RA login is: {$otp}\n\nThis OTP is valid for 10 minutes.", function ($m) use ($user, $otp) {
            $m->to($user->email)->subject('Your RA Login OTP');
        });

        session(['ra_otp_email' => $user->email]);

        return redirect()->route('ra.otp.form')->with('status', 'OTP sent to your email.');
    }

    public function showOtpForm()
    {
        if (!session('ra_otp_email')) {
            return redirect()->route('ra.login');
        }
        return view('auth.ra-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $email = session('ra_otp_email');
        if (!$email) {
            return redirect()->route('ra.login')->withErrors(['otp' => 'Session expired. Please try again.']);
        }

        $user = User::where('email', $email)->first();

        $otpRecord = RaOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        $otpRecord->update(['is_used' => true]);
        session()->forget('ra_otp_email');

        Auth::login($user);

        return redirect()->route('ra.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('ra.login');
    }
}
