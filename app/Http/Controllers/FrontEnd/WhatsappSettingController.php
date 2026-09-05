<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\WhatsappSetting;
use Illuminate\Http\Request;

class WhatsappSettingController extends Controller
{
    public function index()
    {
        $user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $role  = $user->roles()->first()->slug ?? null;

        if ($role === 'root') {
            $setting = WhatsappSetting::firstOrCreate(['organization_id' => $orgId]);
            $templates = WhatsappSetting::all();
        } elseif (in_array($role, ['sadmin','admin'])) {
            $setting = WhatsappSetting::firstOrCreate(['organization_id' => $orgId]);
            $templates = WhatsappSetting::where('organization_id', $orgId)->get();
        } else {
            $setting = WhatsappSetting::where('organization_id', $orgId)->first();
            $templates = WhatsappSetting::where('organization_id', $orgId)->get();
        }

        return view('FrontEnd.whatsapp.form', compact('setting','templates','orgId','role'));
    }



  public function update(Request $request)
{
    $user = \Sentinel::check();
    if (!$user) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Session expired. Please login again.'
        ], 401);
    }

    $role   = $user->roles()->first()->slug ?? null;
    $userId = $user->id;

    $reg = registeruser($userId);
    if (!$reg) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Register profile not found.'
        ], 400);
    }

    $orgId = $reg->organization;

    if (!($role === 'root' || in_array($role, ['admin','sadmin']))) {
        return response()->json([
            'status'  => 'error',
            'message' => 'You are not allowed to edit settings!'
        ], 403);
    }

    // ✅ validate
    $request->validate([
        'provider_url' => 'required|string|max:255',
        'username'     => 'required|string|max:100',
        'password'     => 'required|string|max:100',
        'status'       => 'required|in:0,1',
    ]);

    // ✅ root/admin dono org ke settings edit karenge
    $setting = WhatsappSetting::firstOrCreate([
        'organization_id' => $orgId
    ]);

    $oldData = $setting->toArray();

    // ✅ only allowed fields
    $setting->update([
        'provider_url' => $request->provider_url,
        'username'     => $request->username,
        'password'     => $request->password,
        'status'       => $request->status,
    ]);

    // 🔥 ORG LOG
    logOrganizationActivity(
        $orgId,
        $userId,
        'whatsapp_setting',
        $setting->id,
        'update_whatsapp_setting',
        "WhatsApp settings updated",
        ['old' => $oldData, 'new' => $setting->toArray()]
    );

    // 🔥 USER LOG
    logUserActivity(
        $orgId,
        $userId,
        'whatsapp_setting',
        $setting->id,
        'update_whatsapp_setting',
        "User updated WhatsApp settings",
        ['old' => $oldData, 'new' => $setting->toArray()]
    );

    return response()->json([
        'status'  => 'success',
        'message' => 'WhatsApp settings updated!'
    ]);
}


    public function destroy($id)
    {
        $user   = \Sentinel::check();
        $role   = $user->roles()->first()->slug ?? null;
        $userId = $user->id;

        $reg = registeruser($userId);
        $org = $reg->organization;

        $setting = WhatsappSetting::findOrFail($id);
        $oldData = $setting->toArray();

        if ($role === 'root' || 
            (in_array($role,['admin','sadmin']) && $setting->organization_id == $org)) {

            $setting->delete();

            // 🔥 ORG LOG
            logOrganizationActivity(
                $org,
                $userId,
                'whatsapp_setting',
                $id,
                'delete_whatsapp_setting',
                "WhatsApp settings deleted",
                $oldData
            );

            // 🔥 USER LOG
            logUserActivity(
                $org,
                $userId,
                'whatsapp_setting',
                $id,
                'delete_whatsapp_setting',
                "User deleted WhatsApp settings",
                $oldData
            );

            return response()->json(['status'=>'success','message'=>'Deleted!']);
        }

        return response()->json(['status'=>'error','message'=>'Not allowed!']);
    }
}
