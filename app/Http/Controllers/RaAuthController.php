<?php

namespace App\Http\Controllers;

use App\Models\RaOtp;
use App\Models\User;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\BidDistribution;
use App\Models\NpvpConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RaAuthController extends Controller
{
    public function dashboard()
    {
        $auctions = Auction::whereHas('participants', fn($q) => $q->where('user_id', Auth::id()))
            ->with(['npvCategories', 'npvpConfigurations'])
            ->get();

        return view('app.ra.dashboard', compact('auctions'));
    }

    public function auctionPortal(\App\Models\Auction $auction)
    {
        // Ensure the RA user is a participant of this auction
        abort_unless(
            $auction->participants()->where('user_id', Auth::id())->exists(),
            403
        );

        $auction->load(['npvCategories', 'npvpConfigurations']);

        return view('app.ra.portal', compact('auction'));
    }

    public function placeBid(\Illuminate\Http\Request $request, Auction $auction)
    {
        abort_unless($auction->participants()->where('user_id', Auth::id())->exists(), 403);

        $request->validate([
            'bid_amount'                         => 'required|numeric|min:0.01',
            'distributions'                      => 'required|array|min:1',
            'distributions.*.npv_category_id'    => 'required|exists:npv_categories,id',
            'distributions.*.npvp_config_id'     => 'required|exists:npvp_configurations,id',
            'distributions.*.amount'             => 'required|numeric|min:0',
        ]);

        $bidAmount     = (float) $request->bid_amount;
        $distributions = $request->distributions;

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

        $bid = AuctionBid::create([
            'auction_id'        => $auction->id,
            'user_id'           => Auth::id(),
            'bid_amount'        => $bidAmount,
            'total_distributed' => $totalDistributed,
            'total_npv'         => $totalNpv,
            'status'            => 'confirmed',
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
