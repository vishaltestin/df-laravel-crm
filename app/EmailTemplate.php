<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    /**
     * Table name (optional, Laravel auto-detect bhi kar lega)
     */
    protected $table = 'email_templates';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'organization_id',
        'created_by',
        'title',
        'subject',
        'message',
        'variables_json',
        'status'
    ];

    /**
     * Type casting
     * variables_json ko direct array bana dega
     */
    protected $casts = [
        'variables_json' => 'array',
    ];

    /**
     * Default values
     */
    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * -----------------------------
     * Relationships
     * -----------------------------
     */

    // Email Template → Organization
    public function organization()
    {
        return $this->belongsTo(
            Organization::class,
            'organization_id',
            'id'
        );
    }

    // Email Template → Creator User
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by',
            'id'
        );
    }

    /**
     * -----------------------------
     * Helpers
     * -----------------------------
     */

    /**
     * Subject ke andar variables replace karne ke liye
     */
    public function renderSubject(array $data = [])
    {
        return $this->replaceVariables($this->subject, $data);
    }

    /**
     * Content (HTML email body) ke andar variables replace karne ke liye
     */
    public function renderContent(array $data = [])
    {
        return $this->replaceVariables($this->content, $data);
    }

    /**
     * Core variable replace logic
     */
    protected function replaceVariables(string $text, array $data = [])
    {
        foreach ($data as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }
}
