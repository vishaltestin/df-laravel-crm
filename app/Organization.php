<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';
    protected $fillable = [
        'name', 'email', 'mobile', 'address', 'city', 'state', 'zipcode',
        'country', 'status', 'created_at', 'logo', 'gst','created_by'
    ];
    public $timestamps = false;

   /* public function designations() {
        return $this->hasMany(Designation::class, 'organization_id');
    }

    public function users() {
        return $this->hasMany(User::class, 'organization_id');
    }*/
}
