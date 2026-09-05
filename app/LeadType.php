<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadType extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'status'
    ];
}
