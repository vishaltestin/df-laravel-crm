<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MarketingEvent extends Model
{
    protected $table = 'marketing_events';

    protected $fillable = [
        'name',
        'description',
        'date',
        'organization_id'
    ];
}
