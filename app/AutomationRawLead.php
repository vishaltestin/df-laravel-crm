<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomationRawLead extends Model
{
    protected $table = 'automation_raw_leads';

    protected $fillable = [
        'name', 'mobile', 'course', 'city', 'campaign', 'source',
        'status', 'ivr_response', 'confirm_time',
        'lead_created_status', 'lead_created_time',
    ];

    protected $dates = [
        'confirm_time',
        'lead_created_time',
        'created_at',
        'updated_at',
    ];
}
