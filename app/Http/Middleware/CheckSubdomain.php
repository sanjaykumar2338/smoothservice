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
        // Extract the subdomain from the host
        $host = $request->getHost();
        $subdomain = explode('.', $host)[0];

        // Check if the subdomain is part of the main domain
        $sessionDomain = ltrim(env('SESSION_DOMAIN', 'smoothservice.net'), '.');
        if ($subdomain === $sessionDomain || $subdomain === 'www') {
            // Redirect to workspace form if accessing main domain
            return redirect("https://{$sessionDomain}/workspace");
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
