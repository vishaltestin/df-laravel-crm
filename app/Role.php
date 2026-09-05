<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = [
        'organization_id', 'name', 'status', 'created_at', 'created_by'
    ];
    public $timestamps = false;

    public function designations() {
        return $this->hasMany(Designation::class, 'organization_id');
    }

    public function users() {
        return $this->hasMany(User::class, 'organization_id');
    }
}
