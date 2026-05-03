<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NpvpConfiguration extends Model
{
    protected $fillable = [
        'auction_id',
        'period',
        'percentage_value',
        'index',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', fn($q) => $q->orderBy('index'));
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }
}
