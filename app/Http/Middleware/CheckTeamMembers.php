<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\StripePlan;

class CheckTeamMembers
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $activeSubscription = $user->subscriptions()
            ->where('stripe_status', 'active')
            ->first();

        if ($activeSubscription) {
            $plan = StripePlan::where('id', $activeSubscription->plan_id)->first();
            if ($plan && $plan->team_members >= 5) {
                return redirect()->route('team.list')->with('error', 'You already have the maximum number of team members under your current plan. Please upgrade your plan.');
            }
        }

        return $next($request);
    }
}
