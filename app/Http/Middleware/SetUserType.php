<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Assuming `type` is stored in user model or inferred from the guard
        if (auth('web')->check()) {
            $userType = 'client';
        } elseif (auth('team')->check()) {
            $userType = 'team';
        } else {
            abort(403, 'Unauthorized');
        }

        // Now you can automatically add `userType` to route generation context
        $request->merge(['userType' => $userType]);

        return $next($request);
    }
}
