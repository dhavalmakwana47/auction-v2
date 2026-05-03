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
        'ending_period',
        'initial_npv_value',
    ];

    public function participants()
    {
        return $this->hasMany(AuctionParticipant::class);
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
}
