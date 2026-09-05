<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'app_name',
        'currency',
        'timezone',
        'date_format',
        'logo','payment_credentials',
        'favicon',
        'status'
    ];
protected $casts = [
    'payment_credentials' => 'array',
];



}
