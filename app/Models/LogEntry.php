<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogEntry extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'user_id',
        'event',
        'action',
        'description',
        'entity_type',
        'entity_id',
        'method',
        'url',
        'route_name',
        'ip_address',
        'user_agent',
        'status_code',
        'request_payload',
        'response_payload',
        'meta',
        'occurred_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'meta' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
