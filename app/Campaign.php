<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $table = 'campaigns';

    protected $fillable = [
        'name',
        'subject',
        'status',
        'type',
        'mail_to',
        'spooling',
        'marketing_template_id',
        'marketing_event_id',
        'organization_id',
    ];
}
