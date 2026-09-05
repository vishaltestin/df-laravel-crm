<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\LeadActivityLog;
class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    /**
     * =====================
     * CASTS
     * =====================
     */
    protected $casts = [
        'extra_fields_json'      => 'array',
        'lead_registration_date' => 'datetime',
        'next_followup_at'       => 'datetime',
    ];

    /**
     * =====================
     * FILLABLE
     * =====================
     */
    protected $fillable = [
        // basic
        'name',
        'mobile',
        'alternate_mobile',
        'email',
        'city',
        'state',
        'country',

        // org
        'organization_id',

        // academic / course
        'institute_id',
        'programme_id',
        'faculty_id',
        'application_form_id',

        // source / campaign / event
        'lead_source_id',
        'lead_campaign_id',
        'lead_marketing_event',

        // pipeline
        'pipeline_id',
        'stage_id',
        'type_id',

        // status
        'lead_status_id',
        'lead_sub_status_id',

        // score / weight
        'lead_score',
        'lead_weight_count',

        // assignment
        'lead_assign_to',
        'created_by',

        // dates
        'next_followup_at',
        'lead_registration_date',

        // misc
        'extra_fields_json',
    ];

    /**
     * =====================
     * CORE RELATIONS
     * =====================
     */

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function assignedUser()
    {
        // Sentinel / Register user
        return $this->belongsTo(Register::class, 'lead_assign_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(Register::class, 'created_by');
    }

    /**
     * =====================
     * PIPELINE / STATUS
     * =====================
     */

    public function pipeline()
    {
        return $this->belongsTo(LeadPipeline::class, 'pipeline_id');
    }

    public function stage()
    {
        return $this->belongsTo(LeadPipelineStage::class, 'stage_id');
    }

    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function leadSubStatus()
    {
        return $this->belongsTo(LeadSubStatus::class, 'lead_sub_status_id');
    }

    public function type()
    {
        return $this->belongsTo(LeadType::class, 'type_id');
    }

    /**
     * =====================
     * SOURCE / CAMPAIGN
     * =====================
     */

    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class, 'lead_campaign_id');
    }

    public function marketingEvent()
    {
        return $this->belongsTo(MarketingEvent::class, 'lead_marketing_event');
    }

    /**
     * =====================
     * ACADEMIC / COURSE
     * =====================
     */

    public function institute()
    {
        return $this->belongsTo(Institute::class, 'institute_id');
    }

    public function programme()
    {
        return $this->belongsTo(Program::class, 'programme_id');
    }

    public function faculty()
    {
        // faculty = products table
        return $this->belongsTo(Product::class, 'faculty_id');
    }

    public function applicationForm()
    {
        return $this->belongsTo(ApplicationForm::class, 'application_form_id');
    }

    /**
     * =====================
     * LEAD SUB TABLES
     * =====================
     */

    public function application()
    {
        return $this->hasOne(LeadApplication::class, 'lead_id');
    }

    public function verification()
    {
        return $this->hasOne(LeadVerification::class, 'lead_id');
    }

    public function LeadCommunicationLog()
    {
        return $this->hasMany(LeadCommunicationLog::class, 'lead_id')
            ->orderBy('created_at', 'desc');
    }

    public function notes()
    {
        return $this->hasMany(LeadNote::class, 'lead_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * =====================
     * HELPERS / ACCESSORS
     * =====================
     */

    public function getUploadViaLabelAttribute()
    {
        return ucfirst($this->extra_fields_json['upload_via'] ?? '');
    }

    public function getQualificationNameAttribute()
    {
        return $this->institute->name ?? null;
    }

    public function getProgrammeNameAttribute()
    {
        return $this->programme->name ?? null;
    }

    public function getFacultyNameAttribute()
    {
        return $this->faculty->name ?? null;
    }
public function applicationResponse()
{
    return $this->hasOne(ApplicationFormResponse::class, 'lead_id');
}
	
 public function communications()
{
    return $this->hasMany(CommunicationLog::class, 'lead_id');
}
public function whatsappChats()
{
    return $this->hasMany(WhatsAppLog::class, 'lead_id')->orderBy('created_at', 'asc');
}
public function events()
{
    return $this->hasMany(LeadEvent::class, 'lead_id')->orderBy('start_at', 'asc');
}

 
    // 🔥 THIS IS WHAT WAS MISSING
    public function activities()
    {
        return $this->hasMany(LeadActivityLog::class, 'lead_id')
                    ->orderBy('created_at', 'desc');
    }
	
	
 	
	
	
	
}