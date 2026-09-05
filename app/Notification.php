<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'organization_id',
        'user_id',
        'type',
        'title',
        'body',
        'url',
        'is_read',
        'read_at',
        'ref_type',
        'ref_id',
    ];

    protected $casts = [
        'is_read' => 'integer',
        'read_at' => 'datetime',
    ];
}
