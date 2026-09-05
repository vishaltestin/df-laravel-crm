<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GraphicsSetting extends Model
{
    protected $fillable = [
        'organization_id',
        'theme_color',
        'sidebar_color',
        'navbar_color',
        'login_background',
        'status'
    ];
}
