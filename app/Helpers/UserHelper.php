<?php

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


