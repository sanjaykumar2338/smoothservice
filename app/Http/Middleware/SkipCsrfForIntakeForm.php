<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class SkipCsrfForIntakeForm extends Middleware
{
    protected function inExceptArray($request)
    {
        return $request->is('service/intakeform/update_intake') || parent::inExceptArray($request);
    }

    public function handle($request, Closure $next)
    {
        return parent::handle($request, $next);
    }
}