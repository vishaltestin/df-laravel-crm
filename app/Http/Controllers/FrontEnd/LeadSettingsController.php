<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
 use App\LeadSetting;

class LeadSettingsController extends Controller
{
    private function getRole()
    {
        return Sentinel::check()->roles()->first()->slug ?? null;
    }

    private function getOrgId()
    {
        $reg = registeruser(Sentinel::check()->id);
        return $reg->organization;
    }

    private function canAccess()
    {
        return in_array($this->getRole(), ['root','sadmin','admin','manager']);
    }

    // Show settings page
  public function settings()
{
    if (!Sentinel::check()) abort(401);
    if (!$this->canAccess()) abort(403);

    $orgId = $this->getOrgId();

    // All saved settings of this org
    $settings = LeadSetting::where('organization_id', $orgId)
            ->get()
            ->groupBy('role_slug');


    // Sirf Manager & Operator show karne hain
    $roles = Sentinel::getRoleRepository()
                ->createModel()
                ->whereIn('slug', ['manager','operator'])
                ->get();

    $fields = [
        'mobile' => 'Mobile',
        'email' => 'Email',
        'remarks' => 'Remarks',
        'source' => 'Source',
        'export' => 'Export',
        'custom_fields' => 'Custom Fields'
    ];

    return view('FrontEnd.lead-settings.settings', compact('settings','roles','fields'));
}

    // Save/update settings
  public function saveSettings()
{
    if (!Sentinel::check()) abort(401);
    if (!$this->canAccess()) abort(403);

    $orgId = $this->getOrgId();
    $data = request('settings');

    if (!$data) {
        return back()->with('error', 'No data submitted');
    }

    foreach ($data as $role => $fields) {

        // All possible fields
        $allFields = ['view_mobile','view_email','can_download'];

        foreach ($allFields as $field) {

            LeadSetting::updateOrCreate(
                [
                    'organization_id' => $orgId,
                    'role_slug' => $role,
                    'field' => $field
                ],
                [
                    'can_view' => isset($fields[$field]) ? 1 : 0,
                    'can_download' => ($field == 'can_download' && isset($fields[$field])) ? 1 : 0,
                    'can_edit' => 0
                ]
            );
        }
    }

    return redirect()->back()->with('success', 'Lead settings updated successfully');
}
}
