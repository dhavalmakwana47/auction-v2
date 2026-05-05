<?php

namespace App\Providers;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event) {
            save_log([
                'user_id' => $event->user?->id,
                'event' => 'auth',
                'action' => 'login',
                'description' => 'User logged in.',
                'method' => request()?->method(),
                'url' => request()?->fullUrl(),
                'route_name' => request()?->route()?->getName(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'status_code' => 200,
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            save_log([
                'user_id' => $event->user?->id,
                'event' => 'auth',
                'action' => 'logout',
                'description' => 'User logged out.',
                'method' => request()?->method(),
                'url' => request()?->fullUrl(),
                'route_name' => request()?->route()?->getName(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'status_code' => 200,
            ]);
        });

        Event::listen(Failed::class, function (Failed $event) {
            save_log([
                'user_id' => $event->user?->id,
                'event' => 'auth',
                'action' => 'login_failed',
                'description' => 'Authentication failed.',
                'method' => request()?->method(),
                'url' => request()?->fullUrl(),
                'route_name' => request()?->route()?->getName(),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'status_code' => 401,
                'meta' => [
                    'guard' => $event->guard,
                    'credentials' => array_keys($event->credentials),
                ],
            ]);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
