<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ClientMiddleware
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
        // Check if the user is authenticated via the 'clients' guard
        if (!Auth::guard('client')->check()) {
            // Redirect to the client login page if not authenticated
            return redirect()->route('client.login')->with('error', 'You must be logged in as a client to access this page.');
        }

        return $next($request);
    }
}
