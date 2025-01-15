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
        // Extract the host from the current request
        $host = $request->getHost();

        // Normalize the host
        $normalizedHost = rtrim("https://{$host}", '/'); // Ensure no trailing slash

        // Allow local domain bypass
        if ($host == env('LOCAL_DOMAIN', '127.0.0.1')) {
            return $next($request);
        }

        // Check if the host matches a verified custom domain in the database
        $companySetting = \App\Models\CompanySetting::where(function ($query) use ($normalizedHost) {
            $query->whereRaw("TRIM(TRAILING '/' FROM custom_domain) = ?", [$normalizedHost])
                ->orWhereRaw("TRIM(TRAILING '/' FROM custom_domain) = ?", [rtrim($normalizedHost, '/')]);
        })->where('domain_verified', 1)->first();

        if ($companySetting) {
            // Allow requests for verified custom domains
            return $next($request);
        }

        // Extract the subdomain from the host
        $subdomain = explode('.', $host)[0];

        // Get the session domain from the environment
        $sessionDomain = ltrim(env('SESSION_DOMAIN', 'smoothservice.net'), '.');

        // Allow requests to the main domain or 'www' subdomain
        if ($host === $sessionDomain || $subdomain === 'www') {
            return $next($request); // Allow main domain requests
        }

        // Check if the subdomain exists in the User table
        $user = \App\Models\User::where('workspace', $subdomain)->first();

        if (!$user) {
            // Redirect to the main domain if subdomain doesn't exist
            return redirect("https://{$sessionDomain}");
        }

        // Allow the request to proceed if the subdomain exists
        return $next($request);
    }
}
