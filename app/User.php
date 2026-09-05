<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use Notifiable;
 
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
          'first_name',
        'last_name',
        'email',
        'password',
        'status','permissions','location'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	
	public function organization()
{
    return $this->belongsTo(\App\Organization::class, 'organization_id');
}

public function teams()
{
    return $this->belongsToMany(\App\Team::class, 'team_users', 'user_id', 'team_id');
}
}
