<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
 use App\Designation;

class Register extends Model
{

    protected $fillable = [

	'id','user_id','avatar','user_type','first_name','last_name','email','mobile','user_name','address','notes','organization','designation','team','status','created_at','updated_at','is_available_for_leads','role','other_org'	



    ];


public function designation()
{
    return $this->belongsTo(Designation::class, 'designation');
}
public function designationRel()
{
    return $this->belongsTo(Designation::class, 'designation');
}

}