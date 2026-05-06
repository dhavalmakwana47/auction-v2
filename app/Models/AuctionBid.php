<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionBid extends Model
{
    protected $fillable = ['auction_id', 'user_id', 'bid_amount', 'total_distributed', 'total_npv', 'status', 'ip_address', 'remark', 'revision_backup'];

    public function auction()      { return $this->belongsTo(Auction::class); }
    public function user()         { return $this->belongsTo(User::class); }
    public function distributions(){ return $this->hasMany(BidDistribution::class); }
}
