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

        // Get the session domain from the environment
        $sessionDomain = ltrim(env('SESSION_DOMAIN', 'smoothservice.net'), '.');

        // Check if the subdomain is the main domain or 'www'
        if ($subdomain === $sessionDomain || $subdomain === 'www') {
            // Redirect to workspace form if accessing the main domain
            return redirect("https://{$sessionDomain}/workspace");
        }

        // Check if the subdomain exists in the User table
        $user = User::where('workspace', $subdomain)->first();
        if (!$user) {
            // Return a 404 response if the subdomain doesn't exist
            abort(404, 'Subdomain not found');
        }

        // Allow the request to proceed if subdomain exists
        return $next($request);
    }
}
