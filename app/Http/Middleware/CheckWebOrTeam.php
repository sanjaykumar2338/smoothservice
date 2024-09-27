<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckWebOrTeam
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated via the 'web' (client) or 'team' guard
        if (Auth::guard('web')->check() || Auth::guard('team')->check()) {
            return $next($request); // Allow access if authenticated by either guard
        }

        // If not authenticated, redirect to login page
        return redirect()->route('login');
    }
}
