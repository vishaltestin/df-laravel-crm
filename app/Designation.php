<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    protected $table = 'designation';
    protected $fillable = ['name', 'status', 'created_at', 'organization_id'];
    public $timestamps = false;

    /*public function organization() {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function users() {
        return $this->hasMany(User::class, 'designation', 'id');
    }*/
	
	
	public function organization()
{
    return $this->belongsTo(Organization::class, 'organization_id');
}

}
