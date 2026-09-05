<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadIncomingRaw extends Model
{
    protected $table = 'lead_incoming_raw';

    protected $fillable = [
        'organization_id',
        'provider',
        'external_lead_id',
        'lead_data',
        'is_converted',
    ];

    protected $casts = [
        'lead_data' => 'array',
    ];
}
