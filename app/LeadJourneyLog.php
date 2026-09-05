<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadJourneyLog extends Model
{
    protected $table = 'lead_journey_logs';

    protected $fillable = [
        'organization_id','lead_id','user_id','action','meta_data'
    ];

    public $timestamps = false;
}
