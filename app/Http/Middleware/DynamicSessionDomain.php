<?php
namespace App\Http\Middleware;

use Closure;

class DynamicSessionDomain
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
        // Get the current host (e.g., mymaindomain.com or anothercustomdomain.com)
        $currentDomain = $request->getHost();

        // Set the session cookie dynamically
        config(['session.domain' => $currentDomain]);

        return $next($request);
    }
}
