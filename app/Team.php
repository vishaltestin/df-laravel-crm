<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{    
    protected $table = 'teams';

    protected $fillable = [
        'id',
        'name',
        'organization_id',
        'created_at',
        'status',
        'created_by',
        'head',
        'team_member',
        'updated_at',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
	
	
	public function headUser()
{
    return $this->belongsTo(\App\User::class, 'head');
}

public function memberUsers()
{
    return $this->belongsToMany(\App\User::class, 'teams', 'id', 'team_member');
}


}
