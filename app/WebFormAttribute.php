<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WebFormAttribute extends Model
{
 protected $fillable = [
    'form_id',
    'web_form_id',
    'name',
    'type',
    'placeholder',
    'is_required',
    'is_hidden',
    'sort_order',
    'attribute_id',
    'options',
   
    'created_at',
    'updated_at','default_value'
];


    public function form()
    {
        return $this->belongsTo(WebForm::class);
    }
}
