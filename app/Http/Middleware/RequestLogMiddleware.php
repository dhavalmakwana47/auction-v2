<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class RequestLogMiddleware
{
    private const SENSITIVE_FIELDS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'otp',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = now();
        $preAuthUserId = Auth::id();

        try {
            $response = $next($request);
        } catch (Throwable $exception) {
            $this->persistLog($request, 500, $startedAt, $preAuthUserId, [
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $this->persistLog(
            $request,
            $response->getStatusCode(),
            $startedAt,
            $preAuthUserId
        );

        return $response;
    }

    private function persistLog(
        Request $request,
        int $statusCode,
        $startedAt,
        ?int $preAuthUserId,
        ?array $extraMeta = null
    ): void {
        if ($request->is('broadcasting/auth')) {
            return;
        }

        $method = strtoupper($request->method());
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return;
        }

        $payload = $this->sanitizePayload($request->except(self::SENSITIVE_FIELDS));
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        $routeParams = $route ? $route->parameters() : [];
        $userId = Auth::id() ?? $preAuthUserId;
        $eventDetails = $this->resolveEventDetails($request, $routeName, $routeParams, $statusCode);

        save_log([
            'user_id' => $userId,
            'event' => $eventDetails['event'],
            'action' => $eventDetails['action'],
            'description' => $eventDetails['description'],
            'entity_type' => $this->resolveEntityType($routeParams),
            'entity_id' => $this->resolveEntityId($routeParams),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route_name' => $routeName,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $statusCode,
            'request_payload' => $payload,
            'meta' => array_filter([
                'duration_ms' => now()->diffInMilliseconds($startedAt),
                'route_params' => $routeParams,
                'query' => $request->query(),
                ...($extraMeta ?? []),
            ], static fn($value) => $value !== null),
        ]);
    }

    private function resolveEventDetails(Request $request, ?string $routeName, array $routeParams, int $statusCode): array
    {
        $routeMap = [
            'dashboard' => ['dashboard', 'Dashboard Viewed'],
            'profile.edit' => ['profile', 'Profile Viewed'],
            'profile.update' => ['profile', 'Profile Updated'],
            'profile.destroy' => ['profile', 'Profile Deleted'],
            'login' => ['authentication', 'Login Page Viewed'],
            'logout' => ['authentication', 'Logged Out'],
            'register' => ['authentication', 'Registration Page Viewed'],
            'password.request' => ['authentication', 'Password Reset Page Viewed'],
            'password.email' => ['authentication', 'Password Reset Link Requested'],
            'password.reset' => ['authentication', 'Password Reset Form Viewed'],
            'password.store' => ['authentication', 'Password Reset Submitted'],
            'password.update' => ['authentication', 'Password Updated'],
            'verification.notice' => ['authentication', 'Email Verification Page Viewed'],
            'verification.send' => ['authentication', 'Email Verification Link Sent'],
            'verification.verify' => ['authentication', 'Email Verified'],

            'auctions.index' => ['auction', 'Auctions List Viewed'],
            'auctions.create' => ['auction', 'Auction Form Opened'],
            'auctions.store' => ['auction', 'Auction Created'],
            'auctions.show' => ['auction', 'Auction Details Viewed'],
            'auctions.edit' => ['auction', 'Auction Edit Form Opened'],
            'auctions.update' => ['auction', 'Auction Updated'],
            'auctions.destroy' => ['auction', 'Auction Deleted'],
            'auctions.start-challenge' => ['auction', 'Challenge Started'],
            'auctions.end-challenge' => ['auction', 'Challenge Ended'],
            'auctions.edit-values' => ['auction', 'Auction Values Updated'],
            'auctions.download-report' => ['auction', 'Auction Report Downloaded'],
            'auctions.datatable' => ['auction', 'Auctions Datatable Loaded'],

            'npv-categories.index' => ['npv_category', 'NPV Categories Viewed'],
            'npv-categories.create' => ['npv_category', 'NPV Category Form Opened'],
            'npv-categories.store' => ['npv_category', 'NPV Category Created'],
            'npv-categories.edit' => ['npv_category', 'NPV Category Edit Form Opened'],
            'npv-categories.update' => ['npv_category', 'NPV Category Updated'],
            'npv-categories.destroy' => ['npv_category', 'NPV Category Deleted'],
            'npv-categories.datatable' => ['npv_category', 'NPV Categories Datatable Loaded'],

            'users.index' => ['user_management', 'Users List Viewed'],
            'users.create' => ['user_management', 'User Form Opened'],
            'users.store' => ['user_management', 'User Created'],
            'users.edit' => ['user_management', 'User Edit Form Opened'],
            'users.update' => ['user_management', 'User Updated'],
            'users.destroy' => ['user_management', 'User Deleted'],
            'users.datatable' => ['user_management', 'Users Datatable Loaded'],

            'roles.index' => ['role_management', 'Roles List Viewed'],
            'roles.create' => ['role_management', 'Role Form Opened'],
            'roles.store' => ['role_management', 'Role Created'],
            'roles.edit' => ['role_management', 'Role Edit Form Opened'],
            'roles.update' => ['role_management', 'Role Updated'],
            'roles.destroy' => ['role_management', 'Role Deleted'],
            'roles.datatable' => ['role_management', 'Roles Datatable Loaded'],

            'ra.login' => ['ra_authentication', 'RA Login Page Viewed'],
            'ra.send-otp' => ['ra_authentication', 'RA OTP Sent'],
            'ra.otp.form' => ['ra_authentication', 'RA OTP Form Viewed'],
            'ra.verify-otp' => ['ra_authentication', 'RA OTP Verified'],
            'ra.logout' => ['ra_authentication', 'RA Logged Out'],
            'ra.dashboard' => ['ra_portal', 'RA Dashboard Viewed'],
            'ra.auction.portal' => ['ra_portal', 'RA Auction Portal Viewed'],
            'ra.auction.policy' => ['ra_portal', 'Policy Page Viewed'],
            'ra.auction.policy.sign' => ['ra_portal', 'Policy Signed'],
            'ra.auction.top-bids' => ['ra_portal', 'Top Bids Viewed'],
            'ra.auction.my-bids' => ['ra_portal', 'My Bids Viewed'],
            'ra.auction.bid' => ['ra_portal', 'Bid Submitted'],

            'logs.index' => ['logs', 'Logs List Viewed'],
            'logs.datatable' => ['logs', 'Logs Datatable Loaded'],
            'logs.export' => ['logs', 'Logs Exported'],
        ];

        if ($routeName && isset($routeMap[$routeName])) {
            [$event, $action] = $routeMap[$routeName];

            return [
                'event' => $event,
                'action' => $action,
                'description' => $this->buildDescription($action, $routeName, $routeParams, $statusCode),
            ];
        }

        $fallbackAction = match (strtoupper($request->method())) {
            'POST' => 'Created Resource',
            'PUT', 'PATCH' => 'Updated Resource',
            'DELETE' => 'Deleted Resource',
            default => 'Viewed Resource',
        };

        return [
            'event' => 'http_request',
            'action' => $fallbackAction,
            'description' => $this->buildDescription(
                $fallbackAction,
                $routeName ?: $request->path(),
                $routeParams,
                $statusCode
            ),
        ];
    }

    private function buildDescription(string $action, string $routeName, array $routeParams, int $statusCode): string
    {
        $entityId = $this->resolveEntityId($routeParams);
        $statusText = $statusCode >= 400 ? 'failed' : 'completed';

        return $entityId
            ? sprintf('%s for route "%s" on record #%s (%s).', $action, $routeName, $entityId, $statusText)
            : sprintf('%s for route "%s" (%s).', $action, $routeName, $statusText);
    }

    private function resolveEntityType(array $routeParams): ?string
    {
        foreach ($routeParams as $param) {
            if (is_object($param)) {
                return class_basename($param);
            }
        }

        return null;
    }

    private function resolveEntityId(array $routeParams): ?string
    {
        foreach ($routeParams as $param) {
            if (is_object($param) && method_exists($param, 'getKey')) {
                return (string) $param->getKey();
            }

            if (is_scalar($param)) {
                return (string) $param;
            }
        }

        return null;
    }

    private function sanitizePayload(array $payload): array
    {
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $payload[$key] = $this->sanitizePayload($value);
                continue;
            }

            if (in_array(strtolower((string) $key), self::SENSITIVE_FIELDS, true)) {
                $payload[$key] = '***';
            }
        }

        return $payload;
    }
}
