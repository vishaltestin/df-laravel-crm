<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    /**
     * Table name
     */
    protected $table = 'message_templates';

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
     * Cast fields
     */
    protected $casts = [
        'variables_json' => 'array',
        'editable'       => 'boolean',
        'sent_count'     => 'integer',
    ];

    /**
     * Default values (optional but safe)
     */
    protected $attributes = [
        'editable'   => 1,
        'sent_count' => 0,
        'status'     => 'active',
    ];

    /**
     * Relationships
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * Helpers (optional – future use)
     */
    public function isActive()
    {
        return $this->status === 'active';
    }
}
