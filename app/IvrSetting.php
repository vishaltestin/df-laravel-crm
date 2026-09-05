<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IvrSetting extends Model
{
    protected $table = "ivr_settings";

    protected $fillable = [
        'organization_id',

        'provider',

        'base_url',
        'authcode',
        'default_deskphone',

        'api_key',
        'api_secret',

        'caller_id',
        'welcome_audio',

        'status'
    ];
}
