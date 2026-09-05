<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadSetting extends Model
{
    protected $table = 'lead_settings';

    protected $fillable = [
        'organization_id',
        'role_slug',
        'field',
        'can_view',
        'can_download',
        'can_edit'
    ];

    public $timestamps = true;
}
