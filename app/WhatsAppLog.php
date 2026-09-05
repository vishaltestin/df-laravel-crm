<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
    protected $table = "whatsapp_logs";

    protected $fillable = [
        'organization_id',
        'lead_id',
        'mobile',
        'template_id',
        'message',
        'media_url',
        'provider',
        'provider_message_id',
        'status',
        'response_json',
        'sent_by'
    ];
}
