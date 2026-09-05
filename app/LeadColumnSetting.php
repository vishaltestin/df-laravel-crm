<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadColumnSetting extends Model
{
    protected $table = 'lead_column_settings';

    protected $fillable = [
        'organization_id',
        'user_id',
        'columns_json',
    ];

    protected $casts = [
        'columns_json' => 'array',
    ];
}
