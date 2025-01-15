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
            'user' => 'App\Models\User',
        ]);
    
        $requestHost = request()->getHost();
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
    
        $isDefaultDomain = filter_var($requestHost, FILTER_VALIDATE_IP) || $requestHost === $appHost;
    
        $baseDomain = implode('.', array_slice(explode('.', $requestHost), -2));
        Config::set('session.domain', $isDefaultDomain ? null : '.' . $baseDomain);
    }    
}
