<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\NpvCategory;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;

class AuctionService
{
    public function datatable()
    {
        $auctions = Auction::query();

        return DataTables::eloquent($auctions)
            ->addIndexColumn()
            ->addColumn('increment_type', fn($a) =>
                '<span class="badge-type-' . $a->increment_type . '">' . ucfirst($a->increment_type) . '</span>'
            )
            ->addColumn('initial_npv_value', fn($a) => number_format($a->initial_npv_value, 2))
            ->addColumn('action', fn($a) =>
                '<a href="' . route('auctions.edit', $a) . '" class="btn btn-warning btn-action mr-1" title="Edit"><i class="fas fa-edit" style="font-size:13px;"></i></a>'
                . '<button class="btn btn-danger btn-action btn-delete" data-url="' . route('auctions.destroy', $a) . '" title="Delete"><i class="fas fa-trash" style="font-size:13px;"></i></button>'
            )
            ->rawColumns(['increment_type', 'action'])
            ->make(true);
    }

    public function getParticipants()
    {
        return User::whereHas('roles', fn($q) => $q->where('name', 'ra'))
            ->get(['id', 'name', 'email']);
    }

    public function getNpvCategories()
    {
        return NpvCategory::where('is_active', 1)->pluck('name', 'id');
    }

    public function create(array $data): Auction
    {
        $auction = Auction::create([
            'corporate_debtor_name' => $data['corporate_debtor_name'],
            'meeting_date'          => $data['meeting_date'],
            'base_price'            => $data['base_price'],
            'increment_amount'      => $data['increment_amount'],
            'increment_type'        => $data['increment_type'],
            'process_decleration'   => $data['process_decleration'] ?? null,
            'ending_period'         => $data['ending_period'],
            'initial_npv_value'     => $data['initial_npv_value'],
        ]);

        if (!empty($data['participants'])) {
            $auction->npvCategories()->sync($data['npv_categories']);
        }

        if (!empty($data['npvp'])) {
            foreach ($data['npvp'] as $position => $row) {
                if (!empty($row['period'])) {
                    $auction->npvpConfigurations()->create([
                        'period'           => $row['period'],
                        'percentage_value' => $row['percentage_value'],
                        'index'            => $position + 1,
                    ]);
                }
            }
        }

        return $auction;
    }

    public function update(Auction $auction, array $data): Auction
    {
        $auction->update([
            'corporate_debtor_name' => $data['corporate_debtor_name'],
            'meeting_date'          => $data['meeting_date'],
            'base_price'            => $data['base_price'],
            'increment_amount'      => $data['increment_amount'],
            'increment_type'        => $data['increment_type'],
            'process_decleration'   => $data['process_decleration'] ?? null,
            'ending_period'         => $data['ending_period'],
            'initial_npv_value'     => $data['initial_npv_value'],
        ]);

        $auction->participants()->delete();
        if (!empty($data['participants'])) {
            foreach ($data['participants'] as $userId) {
                $auction->participants()->create(['user_id' => $userId]);
            }
        }

        $auction->npvCategories()->sync($data['npv_categories'] ?? []);

        $auction->npvpConfigurations()->delete();
        if (!empty($data['npvp'])) {
            foreach ($data['npvp'] as $position => $row) {
                if (!empty($row['period'])) {
                    $auction->npvpConfigurations()->create([
                        'period'           => $row['period'],
                        'percentage_value' => $row['percentage_value'],
                        'index'            => $position + 1,
                    ]);
                }
            }
        }

        return $auction;
    }

    public function delete(Auction $auction): void
    {
        $auction->delete();
    }
}
