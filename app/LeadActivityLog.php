<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadActivityLog extends Model
{
    protected $table = 'lead_activity_logs';

    protected $fillable = [
        'organization_id',
        'lead_id',
        'user_id',
        'action',
        'description',
        'meta_data',
        'created_at',
    ];

    protected $casts = [
        'meta_data' => 'array', // ✅ correct
    ];

    public $timestamps = false;

    // (optional but useful)
    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function user()
    {
        return $this->belongsTo(Register::class, 'user_id');
    }
}
