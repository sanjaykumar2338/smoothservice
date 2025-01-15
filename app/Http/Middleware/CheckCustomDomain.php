<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\CompanySetting;

class CheckCustomDomain
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $normalizedHost = rtrim($host, '/');

        $companySetting = CompanySetting::where(function ($query) use ($normalizedHost) {
            $query->whereRaw("TRIM(TRAILING '/' FROM custom_domain) = ?", [$normalizedHost])
                ->orWhereRaw("TRIM(TRAILING '/' FROM custom_domain) = ?", [rtrim($normalizedHost, '/')]);
        })->where('domain_verified', 1)->first();

        if ($companySetting && $request->path() !== 'login') {
            return redirect()->to("https://{$host}/login");
        }

        return $next($request);
    }
}
