<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponService extends Model
{
    use HasFactory;

    protected $table = 'coupon_services';

    protected $fillable = [
        'coupon_id',    // ID of the coupon
        'service_id',   // ID of the service
        'discount',     // Discount value specific to this service
    ];

    /**
     * Relationship with the coupon
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    /**
     * Relationship with the service
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
