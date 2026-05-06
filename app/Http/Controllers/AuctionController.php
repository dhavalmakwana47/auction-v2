<?php

namespace App\Http\Controllers;

use App\Events\BidPlaced;
use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class AuctionController extends Controller
{
    public function __construct(protected AuctionService $auctionService) {}

    public function index()
    {
        return view('app.auctions.index');
    }

    public function dashboard()
    {
        $auctions = Auction::with('npvCategories');
        if (!Auth::user()?->hasRole('admin')) {
            $auctions->where('created_by', Auth::id());
        }
        $auctions = $auctions->get();
        return view('app.index', compact('auctions'));
    }

    public function datatable()
    {
        return $this->auctionService->datatable();
    }

    public function dashboardDatatable()
    {
        return $this->auctionService->dashboardDatatable();
    }

    public function create()
    {
        $participants   = $this->auctionService->getParticipants();
        $npvCategories  = $this->auctionService->getNpvCategories();
        $rpUsers        = $this->auctionService->getRpUsers();
        return view('app.auctions.form', compact('participants', 'npvCategories', 'rpUsers'));
    }

    public function store(Request $request)
    {
        $this->validateAuction($request);
        $this->validateNpvpPeriods($request);

        $this->auctionService->create($request->all());

        return redirect()->route('auctions.index')->with('status', 'Auction created successfully.');
    }

    public function show(Auction $auction)
    {
        $auction->load(['npvCategories', 'npvpConfigurations', 'participants.user', 'bids.user']);
        return view('app.auctions.show', compact('auction'));
    }

    public function startChallenge(Request $request, Auction $auction)
    {
        $request->validate([
            'base_price'       => 'required|numeric|min:0.01',
            'initial_npv_value'=> 'required|numeric|min:0.01',
        ]);

        $auction->update([
            'base_price'        => $request->base_price,
            'initial_npv_value' => $request->initial_npv_value,
            'status'            => 'in_progress',
            'started_at'        => now(),
        ]);

        $auction->load(['npvCategories', 'npvpConfigurations']);

        $basePrice   = (float) $request->base_price;
        $rpUserId    = $auction->created_by;
        $categories  = $auction->npvCategories;
        $configs     = $auction->npvpConfigurations;

        if ($rpUserId && $categories->count() > 0 && $configs->count() > 0) {
            // Distribute base price equally across categories, all into first npvp config
            $firstConfig   = $configs->first();
            $categoryCount = $categories->count();
            $amtPerCat     = round($basePrice / $categoryCount, 2);
            // Adjust last category to avoid rounding gap
            $totalNpv      = 0;
            $distributions = [];

            foreach ($categories as $i => $cat) {
                $amt = ($i === $categoryCount - 1)
                    ? round($basePrice - ($amtPerCat * ($categoryCount - 1)), 2)
                    : $amtPerCat;
                $npvVal         = $amt * (float) $firstConfig->percentage_value;
                $totalNpv      += $npvVal;
                $distributions[] = [
                    'npv_category_id'       => $cat->id,
                    'npvp_configuration_id' => $firstConfig->id,
                    'amount'                => $amt,
                    'npv_value'             => $npvVal,
                ];
            }

            $bid = \App\Models\AuctionBid::create([
                'auction_id'        => $auction->id,
                'user_id'           => $rpUserId,
                'bid_amount'        => $basePrice,
                'total_distributed' => $basePrice,
                'total_npv'         => $totalNpv,
                'status'            => 'confirmed',
                'ip_address'        => $request->ip(),
                'remark'            => 'INITIAL BASE VALUE',
            ]);

            foreach ($distributions as $d) {
                \App\Models\BidDistribution::create(array_merge(['auction_bid_id' => $bid->id], $d));
            }

            $bid->load(['user', 'auction']);
            event(new BidPlaced($bid));
        }

        return response()->json(['message' => 'Challenge round started successfully.']);
    }

    public function editValues(Request $request, Auction $auction)
    {
        $request->validate([
            'base_price'        => 'required|numeric|min:0.01',
            'initial_npv_value' => 'required|numeric|min:0.01',
        ]);

        $auction->update([
            'base_price'        => $request->base_price,
            'initial_npv_value' => $request->initial_npv_value,
        ]);

        return response()->json(['message' => 'Values updated successfully.']);
    }

    public function endChallenge(Auction $auction)
    {
        abort_if($auction->status !== 'in_progress', 403, 'Only in-progress auctions can be ended.');

        $auction->update(['status' => 'completed', 'ended_at' => now()]);

        return response()->json(['message' => 'Challenge process ended. Auction marked as completed.']);
    }

    public function bidsDatatable(Auction $auction)
    {
        $bids = $auction->bids()->with('user')->orderBy('created_at', 'desc');

        return DataTables::eloquent($bids)
            ->addIndexColumn()
            ->addColumn('date_time', fn($b) => $b->created_at->format('d M Y, h:i A'))
            ->addColumn('bid_amount_fmt', fn($b) => '₹ ' . number_format($b->bid_amount))
            ->addColumn('total_npv_fmt', fn($b) => '₹ ' . number_format($b->total_npv, 2))
            ->addColumn('remark_html', fn($b) =>
                '<span class="' . ($b->status === 'confirmed' ? 'text-valid' : 'text-invalid') . '">' .
                '<i class="fas fa-' . ($b->status === 'confirmed' ? 'check-circle' : 'times-circle') . ' mr-1"></i>' .
                e($b->remark ?: ($b->status === 'confirmed' ? 'Valid Bid' : 'Invalid Bid')) .
                '</span>'
            )
            ->rawColumns(['remark_html'])
            ->make(true);
    }

    public function downloadReport(Auction $auction)
    {
        abort_if($auction->status !== 'completed', 403, 'Report is only available for completed auctions.');

        $auction->load([
            'participants.user',
            'bids' => fn($q) => $q->orderBy('created_at'),
            'bids.user',
            'npvpConfigurations',
            'npvCategories',
        ]);

        // Section B: best bid per participant
        $bestBids = [];
        $bidIndexMap = $auction->bids->sortBy('id')->values()->mapWithKeys(fn($b, $i) => [$b->id => $i + 1]);
        foreach ($auction->participants as $p) {
            $best = $auction->bids
                ->where('user_id', $p->user_id)
                ->where('status', 'confirmed')
                ->sortByDesc('bid_amount')
                ->first();

            $bestBids[] = [
                'user'    => $p->user,
                'best'    => $best,
                'annexure'=> 'Annexure ' . (count($bestBids) + 1),
            ];
        }

        // Section C: all bids with remarks
        $basePriceNum = (float) str_replace(',', '', $auction->base_price);
        $incrementNum = (float) str_replace(',', '', $auction->increment_amount);
        $allBids = [];
        $runningBase = $basePriceNum;
        foreach ($auction->bids->sortBy('created_at') as $bid) {
            if ($bid->remark) {
                $remark = $bid->remark;
            } elseif ($bid->status !== 'confirmed') {
                if ($bid->bid_amount <= $runningBase) {
                    $remark = 'BID AMOUNT Less than Base Value';
                } elseif ($auction->increment_type === 'fixed' && $bid->bid_amount < $runningBase + $incrementNum) {
                    $remark = 'BID AMOUNT does not comply with the requirement of Minimum Incremental Bid Value';
                } else {
                    $remark = 'Invalid Bid';
                }
            } else {
                $remark = count($allBids) === 0 ? 'INITIAL BASE VALUE' : '—';
            }
            if ($bid->status === 'confirmed') {
                $runningBase = $bid->bid_amount;
            }
            $allBids[] = ['bid' => $bid, 'base' => $runningBase, 'remark' => $remark];
        }

        $html = view('reports.challenge-report', compact('auction', 'bestBids', 'allBids', 'bidIndexMap'))->render();

        return response($html, 200, [
            'Content-Type'        => 'text/html',
            'Content-Disposition' => 'attachment; filename="challenge-report-' . $auction->id . '.html"',
        ]);
    }

    public function edit(Auction $auction)
    {
        abort_if($auction->status !== 'pending', 403, 'Only pending auctions can be edited.');

        $auction->load('participants', 'npvpConfigurations', 'npvCategories');
        $participants  = $this->auctionService->getParticipants();
        $npvCategories = $this->auctionService->getNpvCategories();
        $rpUsers       = $this->auctionService->getRpUsers();
        return view('app.auctions.form', compact('auction', 'participants', 'npvCategories', 'rpUsers'));
    }

    public function update(Request $request, Auction $auction)
    {
        abort_if($auction->status !== 'pending', 403, 'Only pending auctions can be edited.');

        $this->validateAuction($request);
        $this->validateNpvpPeriods($request);

        $this->auctionService->update($auction, $request->all());

        return redirect()->route('auctions.index')->with('status', 'Auction updated successfully.');
    }

    public function destroy(Auction $auction)
    {
        $this->auctionService->delete($auction);
        return redirect()->route('auctions.index')->with('status', 'Auction deleted successfully.');
    }

    // ── Private helpers ──

    private function validateAuction(Request $request): void
    {
        $request->validate([
            'corporate_debtor_name'   => 'required|string|max:255',
            'meeting_date'            => 'required|string',
            'base_price'              => 'required|string',
            'increment_amount'        => 'required|string',
            'increment_amount_type'    => 'required|in:recommend,mandatory',
            'increment_type'           => 'required|in:fixed,multiple',
            'initial_npv_value'       => 'required|numeric|min:0.01',
            'participants'            => 'nullable|array',
            'participants.*'          => 'exists:users,id',
            'rp_user_id'              => 'nullable|exists:users,id',
            'npv_categories'          => 'required|array|min:1',
            'npv_categories.*'        => 'exists:npv_categories,id',
            'npvp'                    => 'nullable|array',
            'npvp.*.period'           => 'required_with:npvp|string|max:255',
            'npvp.*.percentage_value' => 'required_with:npvp|numeric|min:0|regex:/^\d+(\.\d{1,7})?$/',
        ]);
    }

    private function validateNpvpPeriods(Request $request): void
    {
        // Period is now a free-text field, no ordering validation needed
    }
}
