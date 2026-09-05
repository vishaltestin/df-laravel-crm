<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadCommunicationLog extends Model
{
    protected $table = 'lead_communication_logs';

    protected $fillable = [
        'lead_id',
        'channel',
        'direction',
        'purpose',
        'created_at',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public $timestamps = false;
}
