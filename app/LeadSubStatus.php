<?php
/**
 * change date subscript
 *
 * @param $number
 */
// app/Models/LeadStatus.php
namespace App;
  
use Illuminate\Database\Eloquent\Model;

class LeadSubStatus extends Model
{
    protected $fillable = [
        'lead_status_id',
        'organization_id',
        'name',
        'sort_order',
        'is_active',
    ];

    public function status()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    public function scopeForOrganization($query, $orgId)
    {
        $orgHasCustom = static::where('organization_id', $orgId)->exists();

        if ($orgHasCustom) {
            return $query->where('organization_id', $orgId);
        }

        return $query->whereNull('organization_id');
    }
}
