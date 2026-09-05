<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AutomationRawLeadUpload extends Model
{
    protected $table = 'automation_raw_lead_uploads';

    protected $fillable = [
        'organization_id',
        'user_id',
        'file_name',
        'file_path',
        'total_rows',
        'valid_rows',
        'duplicate_rows',
        'failed_rows',
        'status',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
