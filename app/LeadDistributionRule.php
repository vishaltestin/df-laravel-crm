<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadDistributionRule extends Model
{
    protected $table = 'lead_distribution_rules';

    protected $fillable = [
        'organization_id',
        'rule_name',
        'distribution_type',
        'is_active',
    ];

    public function items()
    {
        return $this->hasMany(LeadDistributionRuleItem::class, 'rule_id');
    }
}
