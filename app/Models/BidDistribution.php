<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BidDistribution extends Model
{
    protected $fillable = ['auction_bid_id', 'npv_category_id', 'npvp_configuration_id', 'amount', 'npv_value'];

    public function bid()              { return $this->belongsTo(AuctionBid::class, 'auction_bid_id'); }
    public function npvCategory()      { return $this->belongsTo(NpvCategory::class); }
    public function npvpConfiguration(){ return $this->belongsTo(NpvpConfiguration::class); }
}
