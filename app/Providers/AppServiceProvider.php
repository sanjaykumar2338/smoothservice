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
    
        $requestHost = request()->getHost();
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
    
        $sessionDomain = (filter_var($requestHost, FILTER_VALIDATE_IP) || $requestHost === $appHost)
            ? null
            : '.' . ltrim($requestHost, '.');
    
        Log::info("SESSION_DOMAIN: {$sessionDomain}");
        config(['session.domain' => $sessionDomain]);
    }    
}
