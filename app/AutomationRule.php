<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomationRule extends Model
{
    protected $table = 'automation_rules';

    protected $fillable = [
        'organization_id',
        'name',
        'trigger_event',
        'conditions',
        'actions',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'actions'    => 'array',
        'is_active'  => 'boolean',
    ];
}
