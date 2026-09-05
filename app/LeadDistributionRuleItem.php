<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadDistributionRuleItem extends Model
{
    protected $table = 'lead_distribution_rule_items';

    protected $fillable = [
        'rule_id',
        'source_id',
        'assign_type',
        'team_id',
        'user_id',
        'designation_id',
        'percentage',
    ];

    public function rule()
    {
        return $this->belongsTo(LeadDistributionRule::class, 'rule_id');
    }
}
