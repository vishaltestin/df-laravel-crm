<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'organization_id',
        'created_by',
        'send_by',
        'title',
        'category',
        'message',
        'variables_json',
        'editable',
        'sent_count',
        'status'
    ];

    /**
     * Casts
     */
    protected $casts = [
        'variables_json' => 'array',
        'editable'       => 'boolean',
        'sent_count'     => 'integer',
    ];

    /**
     * Relationships
     */
    public function organization()
    {
        return $this->belongsTo(\App\Organization::class, 'organization_id');
    }
}
