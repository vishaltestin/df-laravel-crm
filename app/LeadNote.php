<?php
namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadNote extends Model
{
   
    protected $table = 'lead_notes';

    protected $fillable = [

 	'lead_id','note','created_by','created_at','updated_at'
    ];
	
	
	public function creator()
{
    return $this->belongsTo(\App\Register::class, 'created_by');
}

	
	
}

