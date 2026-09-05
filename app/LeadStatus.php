<?php
/**
 * change date subscript
 *
 * @param $number
 */
// app/Models/LeadStatus.php
namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    protected $fillable = [
        'organization_id',
        'name',
        'sort_order',
        'is_active',
    ];

    public function subStatuses()
    {
        return $this->hasMany(LeadSubStatus::class);
    }

    // scope: org के लिए; अगर org के पास custom है तो वही, वरना global
    public function scopeForOrganization($query, $orgId)
    {
        $orgHasCustom = static::where('organization_id', $orgId)->exists();

        if ($orgHasCustom) {
            return $query->where('organization_id', $orgId);
        }

        return $query->whereNull('organization_id'); // global defaults
    }
}
