<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuctionParticipant extends Model
{
    protected $fillable = ['auction_id', 'user_id', 'sign_policy', 'sign_policy_at'];

    protected $casts = [
        'sign_policy'    => 'boolean',
        'sign_policy_at' => 'datetime',
    ];

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
