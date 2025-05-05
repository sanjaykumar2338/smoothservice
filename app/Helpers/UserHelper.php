<?php
use Illuminate\Http\Request;
use App\Models\Service;

if (!function_exists('getUserType')) {
    /**
     * Determine the type of the logged-in user.
     *
     * @return string|null
     */
    function getUserType()
    {
        if (auth('web')->check()) {
            return 'web';
        } elseif (auth('team')->check()) {
            return 'team';
        } elseif (auth('client')->check()) {
            return 'client';
        }

        return 'web';
    }
}

if (!function_exists('checkPermission')) {
    function checkPermission($permission)
    {
        // Check if the user is authenticated via the web guard
        if (auth()->guard('web')->check()) {
            return true;
            //return auth()->guard('web')->user()->hasPermission($permission);
        }
        
        // Check if the user is authenticated via the team guard
        if (auth()->guard('team')->check()) {
            return auth()->guard('team')->user()->hasPermission($permission);
        }

        // If no user is authenticated or no permission is available, return false
        return false;
    }
}

if (!function_exists('getUserID')) {
    function getUserID()
    {
        // Check if the user is authenticated via the web guard
        if (auth()->guard('web')->check()) {
            return auth()->guard('web')->user()->id;
        }
        
        // Check if the user is authenticated via the team guard
        if (auth()->guard('team')->check()) {
            return auth()->guard('team')->user()->id;
        }

        if (auth()->guard('client')->check()) {
            return auth()->guard('client')->user()->id;
        }

        if (auth()->guard('admin')->check()) {
            return auth()->guard('admin')->user()->id;
        }

        // If no user is authenticated or no permission is available, return false
        return false;
    }
}

if (!function_exists('getAuthenticatedUser')) {
    function getAuthenticatedUser()
    {
        // Check if the user is authenticated via the 'web' guard
        if (auth()->guard('web')->check()) {
            return auth()->guard('web')->user();
        }

        // Check if the user is authenticated via the 'team' guard
        if (auth()->guard('team')->check()) {
            return auth()->guard('team')->user();
        }

        if (auth()->guard('client')->check()) {
            return auth()->guard('client')->user();
        }

        // Return null if no user is authenticated
        return null;
    }
}

if (!function_exists('companySetting')) {
    function companySetting()
    {
        $companySettings = \App\Models\CompanySetting::where('user_id', auth()->id())->first();
        if($companySettings){
            return $companySettings;
        }

        return null;
    }
}

if (!function_exists('invoiceSummary')) {
    function invoiceSummary($invoice){
        $summary = [
            'total' => 0,
            'trial_amount' => 0,
            'next_payment_recurring' => 0,
            'total_discount' => 0,
            'payment_type' => 'fixed', // Default to fixed
            'interval' => 1, // Default interval
            'interval_type' => 'month', // Default interval type
        ];
    
        foreach ($invoice->items as $key => $item) {
            $service = $item->service;
    
            // Set interval and interval_type from the first service
            if ($key === 0 && $service->service_type === 'recurring') {
                $summary['interval'] = $service->recurring_service_currency_value_two;
                $summary['interval_type'] = strtolower($service->recurring_service_currency_value_two_type);
                $summary['payment_type'] = 'recurring'; // Update payment type to recurring
            }
    
            // Calculate trial amount if trial is available
            if (!empty($service->trial_for)) {
                $trial_price = $service->trial_price - $item->discount;
                $summary['trial_amount'] += $trial_price * $item->quantity;
    
                // Add recurring payment after the trial
                $next_price = $service->recurring_service_currency_value - $item->discountsnextpayment;
                $summary['next_payment_recurring'] += $next_price * $item->quantity;
            } elseif ($service->service_type === 'recurring') {
                // Add recurring payments for non-trial services
                $price = $service->recurring_service_currency_value - $item->discount;
                $summary['next_payment_recurring'] += $price * $item->quantity;
            } else {
                // For fixed payments, just calculate the total
                $summary['payment_type'] = 'fixed';
            }
    
            // Calculate total and discounts
            $summary['total'] += ($item->price * $item->quantity) - ($item->discount * $item->quantity);
            $summary['total_discount'] += $item->discount * $item->quantity;
        }
    
        // Adjust for upfront payment
        if ($invoice->upfront_payment_amount > 0) {
            $summary['total'] -= $invoice->upfront_payment_amount;
        }

        return $summary;
    }
}

if (!function_exists('notifications')) {
    function notifications($limit = null) {
        $user_id = getUserID();

        $query = \App\Models\History::where('user_id', $user_id)
            ->orderBy('created_at', 'desc');

        if ($limit !== null) {
            $query->limit($limit);
        } else {
            return $query->paginate(20);
        }

        $history = $query->get();
        return $history->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
        });
    }
}

if (!function_exists('servicesSummary')) {
    function servicesSummary(Request $request){
        $summary = [
            'total' => 0,
            'trial_amount' => 0,
            'next_payment_recurring' => 0,
            'total_discount' => 0,
            'payment_type' => 'fixed', // Default to fixed
            'interval' => 1, // Default interval
            'interval_type' => 'month', // Default interval type
        ];
        
        $services = $request->services;
        foreach ($services as $key=>$service) {
            $service = Service::find($service);
    
            // Set interval and interval_type from the first service
            if ($key === 0 && $service->service_type === 'recurring') {
                $summary['interval'] = $service->recurring_service_currency_value_two;
                $summary['interval_type'] = strtolower($service->recurring_service_currency_value_two_type);
                $summary['payment_type'] = 'recurring'; // Update payment type to recurring
            }
    
            // Calculate trial amount if trial is available
            $fixed_price = 0;
            if (!empty($service->trial_for)) {
                $trial_price = $service->trial_price - 0;
                $summary['trial_amount'] += $trial_price * 1;
    
                // Add recurring payment after the trial
                $next_price = $service->recurring_service_currency_value;
                $summary['next_payment_recurring'] += $next_price * 1;
            } elseif ($service->service_type === 'recurring') {
                // Add recurring payments for non-trial services
                $price = $service->recurring_service_currency_value;
                $summary['next_payment_recurring'] += $price * 1;
            } else {
                // For fixed payments, just calculate the total
                $fixed_price = $service->one_time_service_currency_value;
                $summary['payment_type'] = 'fixed';
            }
    
            // Calculate total and discounts
            $summary['total'] += ($fixed_price * 1) - 0;
            $summary['total_discount'] = 0;
        }
    
        // Adjust for upfront payment
        //if ($invoice->upfront_payment_amount > 0) {
        //    $summary['total'] -= $invoice->upfront_payment_amount;
        //}

        return $summary;
    }
}

if (!function_exists('servicesSummary_grapejsfrontent')) {
    function servicesSummary_grapejsfrontent(Request $request) {
        $summary = [
            'total' => 0,
            'trial_amount' => 0,
            'next_payment_recurring' => 0,
            'total_discount' => 0,
            'payment_type' => 'fixed', // Default
            'interval' => 1,
            'interval_type' => 'month',
        ];

        $services = $request->services;

        foreach ($services as $key => $item) {
            $serviceId = $item['service_id'] ?? null;
            $quantity = max(1, intval($item['quantity'] ?? 1));

            if (!$serviceId) continue;

            $service = \App\Models\Service::find($serviceId);
            if (!$service) continue;

            // Set recurring data from first recurring service
            if ($key === 0 && $service->service_type === 'recurring') {
                $summary['interval'] = $service->recurring_service_currency_value_two;
                $summary['interval_type'] = strtolower($service->recurring_service_currency_value_two_type);
                $summary['payment_type'] = 'recurring';
            }

            if (!empty($service->trial_for)) {
                $trialPrice = floatval($service->trial_price);
                $summary['trial_amount'] += $trialPrice * $quantity;

                $nextPrice = floatval($service->recurring_service_currency_value);
                $summary['next_payment_recurring'] += $nextPrice * $quantity;
            } elseif ($service->service_type === 'recurring') {
                $price = floatval($service->recurring_service_currency_value);
                $summary['next_payment_recurring'] += $price * $quantity;
            } else {
                $fixedPrice = floatval($service->one_time_service_currency_value);
                $summary['total'] += $fixedPrice * $quantity;
                $summary['payment_type'] = 'fixed';
            }

            // Optionally apply discount logic here
            $summary['total_discount'] = 0;
        }

        return $summary;
    }
}