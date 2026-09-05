<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomationRuleLog extends Model
{
    protected $table = "automation_rule_logs";

    protected $fillable = [
        'org_id',
        'rule_id',
        'lead_id',
        'trigger_event',
        'action_type',
        'status',
        'message'
    ];
}
