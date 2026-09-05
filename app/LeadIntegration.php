<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadIntegration extends Model
{
    protected $table = 'lead_integrations';

    protected $fillable = [
        'organization_id',
        'provider',
        'is_enabled',
    ];

    public function credentials()
    {
        return $this->hasMany(
            LeadIntegrationCredential::class,
            'organization_id',
            'organization_id'
        )->where('provider', $this->provider);
    }
}
