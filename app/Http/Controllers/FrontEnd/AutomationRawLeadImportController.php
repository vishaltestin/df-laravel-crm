<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\AutomationRawLead;
use App\UserActivityLog;
use App\OrgActivityLog;

use Sentinel;

class AutomationRawLeadImportController extends Controller
{
    // ---------------------------------------------------------------
    // IMPORT RAW LEADS (ONLY INSERT — no WhatsApp, no IVR)
    // ---------------------------------------------------------------
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:4096',
        ]);

        $user = Sentinel::check();
        $orgId = registeruser($user->id)->organization ?? null;

        $file = $request->file('file');
        $path = $file->getRealPath();

        $handle = fopen($path, "r");
        $header = fgetcsv($handle);

        $insertData = [];
        $count = 0;

        while (($row = fgetcsv($handle)) !== false) {

            $data = array_combine($header, $row);

            $insertData[] = [
                'organization_id'     => $orgId,
                'name'                => $data['name'] ?? '',
                'mobile'              => $data['mobile'] ?? '',
                'course'              => $data['course'] ?? '',
                'city'                => $data['city'] ?? '',
                'campaign'            => $data['campaign'] ?? '',
                'source'              => $data['source'] ?? '',
                'status'              => 'pending',
                'lead_created_status' => 0,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];

            $count++;
        }

        fclose($handle);

        // Bulk Insert
        DB::transaction(function () use ($insertData) {
            foreach (array_chunk($insertData, 500) as $chunk) {
                AutomationRawLead::insert($chunk);
            }
        });

        // Log import
        $this->log($user->id, $orgId, "raw_lead.imported", [
            "count" => $count
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'CSV imported successfully.',
            'count'   => $count
        ]);
    }


    // ---------------------------------------------------------------
    // LOGGING HELPER
    // ---------------------------------------------------------------
    private function log($userId, $orgId, $event, $meta = [])
    {
        $ip = request()->ip();
        $agent = request()->header('User-Agent');
        $meta_json = json_encode($meta);

        try {
            UserActivityLog::create([
                'user_id'        => $userId,
                'organization_id'=> $orgId,
                'event'          => $event,
                'meta_json'      => $meta_json,
                'ip_address'     => $ip,
                'user_agent'     => $agent,
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {}

        try {
            OrgActivityLog::create([
                'organization_id'=> $orgId,
                'event'          => $event,
                'meta_json'      => $meta_json,
                'ip_address'     => $ip,
                'user_agent'     => $agent,
                'created_at'     => now(),
            ]);
        } catch (\Throwable $e) {}
    }
}
