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

    public function datatable()
    {
        return $this->auctionService->datatable();
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

    public function edit(Auction $auction)
    {
        $auction->load('participants', 'npvpConfigurations', 'npvCategories');
        $participants   = $this->auctionService->getParticipants();
        $npvCategories  = $this->auctionService->getNpvCategories();
        return view('app.auctions.form', compact('auction', 'participants', 'npvCategories'));
    }

    public function update(Request $request, Auction $auction)
    {
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
            'increment_amount_type'    => 'required|in:Recommend,Mandatory',
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
