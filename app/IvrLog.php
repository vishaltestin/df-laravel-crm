<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IvrLog extends Model
{
    protected $table = "ivr_logs";

    protected $fillable = [
        'organization_id',
        'lead_id',
        'mobile',
        'dtmf',
        'call_id',
        'status',
        'provider',
        'response_json'
    ];
}
