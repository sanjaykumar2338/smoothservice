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
        // Check if the user is authenticated via 'web' or 'team' guard
        if (Auth::guard('web')->check()) {
            return $next($request);
        } elseif (Auth::guard('team')->check()) {
            return $next($request);
        }

        // If not authenticated, redirect to a generic login route or based on the guard
        return redirect()->route('login'); // You can change 'login' to your actual login route
    }
}
