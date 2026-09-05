<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadWebhookLog extends Model
{
    protected $table = 'lead_webhook_logs';

    protected $fillable = [
        'organization_id',
        'provider',
        'event_type',
        'request_headers',
        'payload',
        'is_processed',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'request_headers' => 'array',
    ];

    public $timestamps = false;
}
