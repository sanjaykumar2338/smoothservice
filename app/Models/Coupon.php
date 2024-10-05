<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'coupon_code',
        'description',
        'discount_type',  // 'Fixed' or 'Percentage'
        'discount_value', // The discount amount
        'applies_to',     // 'All services' or specific services
        'limit_per_user', // Limit of coupons per user
        'expiry_date',    // Expiry date of the coupon
        'min_cart_value', // Minimum cart amount to apply coupon
        'redeem_type',    // One-time use or recurring
        'status',         // 'Active' or 'Inactive'
        'added_by',       // ID of the user who created the coupon
        'limit_to_one',
        'limit_to_new_customers',
        'set_expiry',
        'min_cart_amount',
        'min_cart_amount_value'
    ];

    /**
     * Relationship with the user who created the coupon
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Relationship to fetch services the coupon applies to
     * Includes the discount per service.
     */
    public function services()
    {
        return $this->belongsToMany(Service::class, 'coupon_services', 'coupon_id', 'service_id')
                    ->withPivot('discount') // Include the discount per service
                    ->withTimestamps();
    }

    /**
     * Check if the coupon applies to all services
     */
    public function appliesToAllServices()
    {
        return $this->applies_to === 'All services';
    }

    /**
     * Check if the coupon is active
     */
    public function isActive()
    {
        return $this->status === 'Active' && (!$this->expiry_date || $this->expiry_date >= now());
    }

    /**
     * Check if the coupon has a minimum cart value requirement
     */
    public function hasMinCartValue()
    {
        return !is_null($this->min_cart_value) && $this->min_cart_value > 0;
    }

    /**
     * Determine if the coupon is valid for a given user (based on limits and active status)
     */
    public function isValidForUser($user)
    {
        // Example logic, assuming there's a way to track coupon usage
        $userCouponCount = $this->userCoupons()->where('user_id', $user->id)->count();

        return $this->isActive() && ($this->limit_per_user === null || $userCouponCount < $this->limit_per_user);
    }

    /**
     * Relationship to track user-specific coupon usage
     */
    public function userCoupons()
    {
        // Assuming there's a table tracking user coupon usage
        return $this->hasMany(UserCoupon::class, 'coupon_id');
    }
}
