<?php
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationLog extends Model
{
   
    protected $table = 'communication_logs';

    protected $fillable = [

 	
 	'organization_id','lead_id','channel','sent_by','to','subject','content','created_at','updated_at'	,'sent_at','status' 
    ];
	
	
	public function smsLog()
{
    return $this->hasOne(\App\SmsLog::class, 'communication_log_id', 'id');
}

public function emailLog()
{
    return $this->hasOne(\App\EmailLog::class, 'communication_log_id', 'id');
}

public function whatsappLog()
{
    return $this->hasOne(\App\WhatsappLog::class, 'communication_log_id', 'id');
}

	
	
}

