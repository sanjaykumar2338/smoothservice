<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        echo "test mmmm"; die;
        // Extract the subdomain from the host
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        if ($host == env('LOCAL_DOMAIN', '127.0.0.1')) {
            return $next($request);
        }

        // Get the session domain from the environment
        $sessionDomain = ltrim(env('SESSION_DOMAIN', 'smoothservice.net'), '.');

        // Allow the main domain to proceed without redirecting
        if ($host === $sessionDomain || $subdomain === 'www') {
            return $next($request); // Allow main domain requests
        }

        // Check if the subdomain exists in the User table
        $user = User::where('workspace', $subdomain)->first();

        if (!$user) {
            // Redirect to the main domain if subdomain doesn't exist
            return redirect("https://{$sessionDomain}");
        }

        // Allow the request to proceed if subdomain exists
        return $next($request);
    }
}
