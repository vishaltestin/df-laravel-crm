<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $table = 'attributes';

    protected $fillable = [
        'code',
        'name',
        'type',
        'lookup_type',
        'entity_type',
        'sort_order',
        'validation',
        'is_required',
        'is_unique',
        'quick_add',
        'is_user_defined',
        'organization_id',
    ];
}
