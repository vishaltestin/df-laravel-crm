<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadApplication extends Model
{
    protected $table = 'lead_applications';

    protected $fillable = [
        'lead_id',
        'application_form',
        'application_score',
        'payment_status',
        'payment_method',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
	 public function form()
	{
		return $this->belongsTo(ApplicationForm::class, 'application_form');
	}

    public $timestamps = false;
}
