<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'client' => 'App\Models\Client',
            'user' => 'App\Models\User',
        ]);

        // Get the current request host
        $requestHost = request()->getHost();
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        // Determine the session domain dynamically
        $sessionDomain = (filter_var($requestHost, FILTER_VALIDATE_IP) || $requestHost === $appHost)
            ? null
            : '.' . ltrim($requestHost, '.');

        // Set the session domain configuration
        config(['session.domain' => $sessionDomain]);
    }
}
