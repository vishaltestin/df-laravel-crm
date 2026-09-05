<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadAssignmentLog extends Model
{
    protected $table = 'lead_assignment_logs';

    protected $fillable = [
        'lead_id',
        'organization_id',
        'lead_source_id',
        'assigned_to',
        'previous_assigned_to',
        'rule_type',
        'rule_id',
        'is_reassign',
        'notes',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
