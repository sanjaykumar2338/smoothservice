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
        }

        return 'web';
    }
}

if (!function_exists('checkPermission')) {
    function checkPermission($permission=false)
    {
        return auth()->user()->hasPermission($permission);
    }
}
