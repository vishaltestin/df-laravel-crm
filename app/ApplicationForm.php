<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApplicationForm extends Model
{
    protected $table = 'application_forms';

    protected $fillable = [
        'title',
        'description',
        'html_template',
        'form_name',
        'status',
        'allowed_countries',
        'allowed_institutes',
        'fees',
        'tax',
        'total_amount',
        'gateway',
    ];

    protected $casts = [
        'allowed_countries'  => 'array',
        'allowed_institutes' => 'array',
    ];

    public function responses()
    {
        return $this->hasMany(
            ApplicationFormResponse::class,
            'lead_form_id'
        );
    }
	
	 public function leadApplications()
	{
		return $this->hasMany(LeadApplication::class, 'application_form');
	}

 

}



