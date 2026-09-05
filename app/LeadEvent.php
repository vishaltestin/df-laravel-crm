<?php
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadEvent extends Model
{
   
    protected $table = 'lead_events';

    protected $fillable = [

 	'lead_id','subject','description','start_at','end_at','owner_id','created_at','updated_at','event_type'
    ];
	
	
	
	
	
}

