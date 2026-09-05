<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'provider',
        'api_key',
        'sender_id',
        'route',
        'status','created_at','updated_at','api_url','http_method','mobile_param','message_param','extra_params'	
     ];
}
