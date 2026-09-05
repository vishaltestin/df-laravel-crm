<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class ProductTag extends Model
{
    protected $fillable = ['tag_id','product_id'];
}
