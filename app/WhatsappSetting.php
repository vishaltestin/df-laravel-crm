<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
   protected $fillable = [
    'organization_id',
    'provider',
    'api_key',
    'sender_number',
    'template_namespace',
    'status',

    // new
    'provider_url',
    'username',
    'password',
];

}
