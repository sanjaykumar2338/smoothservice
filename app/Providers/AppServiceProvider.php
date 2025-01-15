<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Config;

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
            'user' => 'App\Models\User', // Add other sender types here
        ]);

        // Get the current request host
        $requestHost = request()->getHost();

        // Extract the app's base host from the configured app URL
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        // Check if the request host is an IP address or matches the app's host
        $isDefaultDomain = filter_var($requestHost, FILTER_VALIDATE_IP) || $requestHost === $appHost;

        // Dynamically set the SESSION_DOMAIN
        Config::set('session.domain', $isDefaultDomain ? null : '.' . $requestHost);
    }
}
