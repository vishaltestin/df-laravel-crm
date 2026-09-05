<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicationFormResponse extends Model
{
    protected $table = 'application_form_responses';

    protected $fillable = ['lead_form_id', 'response_json', 'fill_percentage', 'email_verified', 'otp_verified', 'lead_id'];

    public function form()
    {
        return $this->belongsTo(ApplicationForm::class, 'lead_form_id');
		}
		
		
		  public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function application()
    {
        return $this->belongsTo(LeadApplication::class, 'lead_form_id');
    }
}
