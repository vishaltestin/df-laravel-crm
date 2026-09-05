<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'status',
        'lead_count',
        'billing_setup_amount'
    ];
	
	public function organization()
{
    return $this->belongsTo(\App\Organization::class, 'organization_id', 'id');
}

}
