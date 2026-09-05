<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\GraphicsSetting;
use Illuminate\Http\Request;

class GraphicsSettingController extends Controller
{
    public function index($orgId = null)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;
        $role  = $user->roles()->first()->slug ?? null;

        if ($role === 'root') {
            $setting = GraphicsSetting::firstOrCreate(['organization_id' => $orgId]);
        } elseif (in_array($role, ['admin', 'sadmin'])) {
            $setting = GraphicsSetting::firstOrCreate(['organization_id' => $orgId]);
        } else {
            $setting = GraphicsSetting::where('organization_id', $orgId)->first();
        }

        return view('FrontEnd.graphics.form', compact('setting', 'orgId', 'role'));
    }



    public function update(Request $request)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;
        $role   = $user->roles()->first()->slug ?? null;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $setting = GraphicsSetting::where('organization_id', $orgId)->first();

        if (!$setting) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Setting not found for this organization!'
            ]);
        }

        if (!($role === 'root' || in_array($role, ['admin', 'sadmin']))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You are not allowed to edit Graphics settings!'
            ]);
        }

        $oldData = $setting->toArray();

        // Update Settings
        $setting->update($request->all());

        // 🔥 ORG LOG
        logOrganizationActivity(
            $orgId,
            $userId,
            'graphics_setting',
            $setting->id,
            'update_graphics_setting',
            "Graphics setting updated for organization",
            ['old' => $oldData, 'new' => $request->all()]
        );

        // 🔥 USER LOG
        logUserActivity(
            $orgId,
            $userId,
            'graphics_setting',
            $setting->id,
            'update_graphics_setting',
            "User updated graphics setting",
            ['old' => $oldData, 'new' => $request->all()]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'Graphics settings updated!'
        ]);
    }



    public function destroy($id)
    {
        $user   = \Sentinel::check();
        $userId = $user->id;
        $role   = $user->roles()->first()->slug ?? null;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;

        $template = GraphicsSetting::findOrFail($id);

        if ($role === 'root' || (in_array($role, ['admin','sadmin']) && $template->organization_id == $orgId)) {

            $oldData = $template->toArray();
            $template->delete();

            // 🔥 ORG LOG
            logOrganizationActivity(
                $orgId,
                $userId,
                'graphics_setting',
                $id,
                'delete_graphics_setting',
                "Graphics setting deleted",
                $oldData
            );

            // 🔥 USER LOG
            logUserActivity(
                $orgId,
                $userId,
                'graphics_setting',
                $id,
                'delete_graphics_setting',
                "User deleted graphics setting",
                $oldData
            );

            return response()->json([
                'status'  => 'success',
                'message' => 'Deleted!'
            ]);
        }

        return response()->json([
            'status'  => 'error',
            'message' => 'You are not allowed to delete this record!'
        ]);
    }
}

