<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    protected $table = "sms_logs";

    protected $fillable = [
        'organization_id',
        'lead_id',
        'mobile',
        'message',
        'provider',
        'provider_message_id',
        'status',
        'response_json',
        'sent_by','full_url'
    ];
}
