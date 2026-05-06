<?php

namespace App\Services;

use App\Mail\RaInvitationMail;
use App\Models\Auction;
use App\Models\NpvCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class AuctionService
{
    public function datatable()
    {
        $auctions = Auction::query();
        if (!Auth::user()?->hasRole('admin')) {
            $auctions->where('created_by', Auth::id());
        }

        return DataTables::eloquent($auctions)
            ->addIndexColumn()
            ->addColumn('increment_type', fn($a) =>
                '<span class="badge-type-' . $a->increment_type . '">' . ucfirst($a->increment_type) . '</span>'
            )
            ->addColumn('initial_npv_value', fn($a) => number_format($a->initial_npv_value, 2))
            ->addColumn('created_date', fn($a) => $a->created_at ? $a->created_at->format('d M Y') : '—')
            ->addColumn('action', function ($a) {
                $buttons = '';
                if ($a->status === 'pending') {
                    $buttons .= '<a href="' . route('auctions.edit', $a) . '" class="btn btn-warning btn-action mr-1" title="Edit"><i class="fas fa-edit" style="font-size:13px;"></i></a>';
                }
                $buttons .= '<button class="btn btn-danger btn-action btn-delete" data-url="' . route('auctions.destroy', $a) . '" title="Delete"><i class="fas fa-trash" style="font-size:13px;"></i></button>';
                return $buttons;
            })
            ->rawColumns(['increment_type', 'action'])
            ->make(true);
    }

    public function dashboardDatatable()
    {
        $auctions = Auction::query();
        if (!Auth::user()?->hasRole('admin')) {
            $auctions->where('created_by', Auth::id());
        }

        $status = request('status', 'pending');
        $auctions->where('status', $status);

        return DataTables::eloquent($auctions)
            ->addIndexColumn()
            ->addColumn('increment_type', fn($a) =>
                '<span class="badge-type-' . $a->increment_type . '">' . ucfirst($a->increment_type) . '</span>'
            )
            ->addColumn('initial_npv_value', fn($a) => number_format($a->initial_npv_value, 2))
            ->addColumn('status', fn($a) =>
                '<span class="badge-status-' . $a->status . '">' . ucfirst(str_replace('_', ' ', $a->status)) . '</span>'
            )
            ->rawColumns(['increment_type', 'status'])
            ->make(true);
    }

    public function getParticipants()
    {
        return User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) = ?', ['ra']))
            ->get(['id', 'name', 'email']);
    }

    public function getRpUsers()
    {
        return User::whereHas('roles', fn($q) => $q->whereRaw('LOWER(name) IN (?)', ['Resolution Professional (RP)']))
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
            'increment_amount_type' => $data['increment_amount_type'],
            'increment_type'        => $data['increment_type'],
            'process_decleration'   => $data['process_decleration'] ?? null,
            'initial_npv_value'     => $data['initial_npv_value'],
            'created_by'            => !empty($data['rp_user_id']) ? $data['rp_user_id'] : Auth::id(),
        ]);

        if (!empty($data['participants'])) {
            foreach ($data['participants'] as $userId) {
                $auction->participants()->create(['user_id' => $userId]);
            }
        }

        if (!empty($data['npv_categories'])) {
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

        if (!empty($data['participants'])) {
            $raUsers = User::whereIn('id', $data['participants'])->get();
            foreach ($raUsers as $ra) {
                Mail::to($ra->email)->send(new RaInvitationMail($auction, $ra));
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
            'initial_npv_value'     => $data['initial_npv_value'],
        ]);

        $existingUserIds = $auction->participants()->pluck('user_id')->toArray();
        $newUserIds = array_diff($data['participants'] ?? [], $existingUserIds);

        $auction->participants()->delete();
        if (!empty($data['participants'])) {
            foreach ($data['participants'] as $userId) {
                $auction->participants()->create(['user_id' => $userId]);
            }
        }

        if (!empty($newUserIds)) {
            $newRaUsers = User::whereIn('id', $newUserIds)->get();
            foreach ($newRaUsers as $ra) {
                Mail::to($ra->email)->send(new RaInvitationMail($auction, $ra));
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
