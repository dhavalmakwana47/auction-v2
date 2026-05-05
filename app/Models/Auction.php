<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    protected $fillable = [
        'corporate_debtor_name',
        'meeting_date',
        'base_price',
        'increment_amount',
        'increment_amount_type',
        'increment_type',
        'process_decleration',
        'status',
        'initial_npv_value',
        'created_by',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
    ];

    public function participants()
    {
        return $this->hasMany(AuctionParticipant::class);
    }

    public function bids()
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'auction_participants');
    }

    public function npvpConfigurations()
    {
        return $this->hasMany(NpvpConfiguration::class);
    }

    public function npvCategories()
    {
        return $this->belongsToMany(NpvCategory::class, 'auction_npv_categories');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
