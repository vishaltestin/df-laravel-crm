<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{

    protected $fillable = [
        'name', 'alt', 'category_id','type','filename','orderby','status','created_at','updated_at'
    ];

}
