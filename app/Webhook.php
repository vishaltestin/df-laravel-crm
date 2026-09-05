<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    protected $table = 'webhooks';

    protected $fillable = [
        'name',
        'entity_type',
        'description',
        'method',
        'end_point',
        'query_params',
        'headers',
        'payload_type',
        'raw_payload_type',
        'payload',
        'organization_id'
    ];

    protected $casts = [
        'query_params' => 'array',
        'headers' => 'array',
        'payload' => 'array',
    ];
}
