<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaOtp extends Model
{
    protected $fillable = ['user_id', 'otp', 'expires_at', 'is_used'];

    protected $casts = ['expires_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isValid(): bool
    {
        return !$this->is_used && $this->expires_at->isFuture();
    }
}
