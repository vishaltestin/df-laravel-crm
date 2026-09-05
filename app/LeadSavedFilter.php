<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadSavedFilter extends Model
{
    protected $table = 'lead_saved_filters';

   protected $fillable = [
    'organization_id',
    'user_id',
    'name',
    'filters_json',
    'columns_json',
    'sort_json',
    'is_default',
];

    protected $casts = [
        'filters_json' => 'array',
        'columns_json' => 'array',
        'sort_json' => 'array',
        'is_default' => 'boolean',
    ];
}
