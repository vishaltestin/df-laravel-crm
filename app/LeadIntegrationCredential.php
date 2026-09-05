<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadIntegrationCredential extends Model
{
    protected $table = 'lead_integration_credentials';

    protected $fillable = [
        'organization_id',
        'provider',
        'key',
        'value',
        'is_encrypted',
    ];

    protected $hidden = ['value'];

    public function getDecryptedValueAttribute()
    {
        return $this->is_encrypted
            ? decrypt($this->value)
            : $this->value;
    }
}
