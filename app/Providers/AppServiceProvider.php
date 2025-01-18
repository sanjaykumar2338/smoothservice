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
        // Morph map configuration
        Relation::morphMap([
            'client' => 'App\Models\Client',
            'user' => 'App\Models\User',
        ]);

        // Get the current request host and the application's main domain
        $requestHost = request()->getHost(); // Current host (e.g., force.smoothservice.net)
        $appHost = parse_url(config('app.url'), PHP_URL_HOST); // Main domain (e.g., smoothservice.net)

        // Determine the session domain dynamically
        $sessionDomain = (filter_var($requestHost, FILTER_VALIDATE_IP) || $requestHost === $appHost)
            ? null // Do not set a domain for IPs or the main domain
            : '.' . ltrim($appHost, '.'); // Use the main domain with a leading dot for subdomains

        // Dynamically configure the session domain
        config(['session.domain' => $sessionDomain]);
    }
}
