<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadVerification extends Model
{
    protected $table = 'lead_verifications';

    protected $primaryKey = 'lead_id';
    public $incrementing = false;

    protected $fillable = [
        'lead_id',
        'sms_verified',
        'email_verified',
        'verified_at',
    ];

    public $timestamps = false;

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}
