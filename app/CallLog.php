<?php
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallLog  extends Model
{
   
    protected $table = 'call_logs';

    protected $fillable = [

 	
 	'lead_id', 'agent_id', 'direction', 'duration', 'recording_url', 'summary', 'notes', 'created_at','updated_at'	 
    ];
	
	
	
	
	
}

