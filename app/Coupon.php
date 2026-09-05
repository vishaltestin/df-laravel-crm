<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'coupons';

    protected $fillable = [
        'organization_id',
        'code',
        'name',
        'type',
        'value',
        'min_amount',
        'max_discount',
        'usage_type',
        'usage_limit',
        'used_count',
        'start_at',
        'end_at',
        'status',
        'created_by'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];
}
