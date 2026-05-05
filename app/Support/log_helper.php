<?php

use App\Models\LogEntry;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

if (!function_exists('save_log')) {
    /**
     * Persist a structured application log row.
     */
    function save_log(array $payload): ?LogEntry
    {
        try {
            $userId = $payload['user_id'] ?? Auth::id();
            if ($userId !== null && !User::whereKey($userId)->exists()) {
                $userId = null;
            }

            return LogEntry::create([
                'user_id' => $userId,
                'event' => $payload['event'] ?? 'request',
                'action' => $payload['action'] ?? 'unknown',
                'description' => $payload['description'] ?? null,
                'entity_type' => $payload['entity_type'] ?? null,
                'entity_id' => $payload['entity_id'] ?? null,
                'method' => $payload['method'] ?? null,
                'url' => $payload['url'] ?? null,
                'route_name' => $payload['route_name'] ?? null,
                'ip_address' => $payload['ip_address'] ?? null,
                'user_agent' => $payload['user_agent'] ?? null,
                'status_code' => $payload['status_code'] ?? null,
                'request_payload' => $payload['request_payload'] ?? null,
                'response_payload' => $payload['response_payload'] ?? null,
                'meta' => $payload['meta'] ?? null,
                'occurred_at' => $payload['occurred_at'] ?? Carbon::now(),
            ]);
        } catch (\Throwable $exception) {
            report($exception);
            return null;
        }
    }
}
