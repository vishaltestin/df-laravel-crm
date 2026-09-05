<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $table = 'coupon_usages';

    public $timestamps = false;

    protected $fillable = [
        'organization_id',
        'coupon_id',
        'lead_id',
        'application_id',
        'used_by_user_id',
        'used_at',
        'discount_amount',
        'order_amount',
        'meta_json',
        'created_at'
    ];
}
