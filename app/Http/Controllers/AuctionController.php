<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuctionController extends Controller
{
    public function __construct(protected AuctionService $auctionService) {}

    public function index()
    {
        return view('app.auctions.index');
    }

    public function dashboard()
    {
        $auctions = Auction::with('npvCategories')->get();
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
        return view('app.auctions.form', compact('participants', 'npvCategories'));
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
            $remark = '';
            if ($bid->status !== 'confirmed') {
                if ($bid->bid_amount <= $runningBase) {
                    $remark = 'BID AMOUNT Less than Base Value';
                } elseif ($auction->increment_type === 'fixed' && $bid->bid_amount < $runningBase + $incrementNum) {
                    $remark = 'BID AMOUNT does not comply with the requirement of Minimum Incremental Bid Value';
                } else {
                    $remark = 'Invalid Bid';
                }
            } else {
                $remark = count($allBids) === 0 ? 'INITIAL BASE VALUE' : '—';
                $runningBase = $bid->bid_amount;
            }
            $allBids[] = ['bid' => $bid, 'base' => $runningBase, 'remark' => $remark];
        }

        $html = view('reports.challenge-report', compact('auction', 'bestBids', 'allBids'))->render();

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
        return view('app.auctions.form', compact('auction', 'participants', 'npvCategories'));
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
            'ending_period'           => 'required|integer|min:1',
            'initial_npv_value'       => 'required|numeric|min:0.01',
            'participants'            => 'nullable|array',
            'participants.*'          => 'exists:users,id',
            'npv_categories'          => 'required|array|min:1',
            'npv_categories.*'        => 'exists:npv_categories,id',
            'npvp'                    => 'nullable|array',
            'npvp.*.period'           => 'required_with:npvp|integer|min:1',
            'npvp.*.percentage_value' => 'required_with:npvp|numeric|min:0|regex:/^\d+(\.\d{1,7})?$/',
        ]);
    }

    private function validateNpvpPeriods(Request $request): void
    {
        $rows = $request->input('npvp', []);
        $errors = [];
        $prevPeriod = 0;
        $maxPeriod  = 0;

        foreach ($rows as $i => $row) {
            $period = (int) ($row['period'] ?? 0);

            if ($i > 0 && $period <= $prevPeriod) {
                $errors["npvp.{$i}.period"] = [
                    "Row " . ($i + 1) . ": Period ({$period}) must be greater than previous row's period ({$prevPeriod})."
                ];
            }

            if ($period > $maxPeriod) $maxPeriod = $period;
            $prevPeriod = $period;
        }

        $endingPeriod = (int) $request->input('ending_period', 0);
        if (!empty($rows) && $endingPeriod <= $maxPeriod) {
            $errors['ending_period'] = [
                "Ending period must be greater than the maximum NPVP period ({$maxPeriod})."
            ];
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }
}
