<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\AutomationRawLead;
use App\Lead;
use App\UserActivityLog;
use App\OrgActivityLog;
use App\LeadActivityLog;
use App\Jobs\ProcessRawLeadUploadJob;
use Sentinel;
use Illuminate\Support\Facades\Storage;
use App\AutomationRawLeadUpload;
class AutomationRawLeadController extends Controller
{
 
 private function getOrgId()
    {
        $user = Sentinel::check();
        $reg = registeruser($user->id);
        return $reg->organization ?? null;
    }

   // -------------------- INDEX --------------------
    // Raw data listing + Ajax response
 public function index(Request $request)
{
    $orgId = $this->getOrgId();

    // ✅ AJAX response for raw leads listing
    if ($request->ajax == 1) {

        $query = AutomationRawLead::where('organization_id', $orgId);

        // upload wise filter
        if ($request->upload_id) {
            $query->where('upload_id', $request->upload_id);
        }

        $rows = $query->orderBy('id', 'DESC')->paginate(50);

        return response()->json([
            'status' => true,
            'data' => $rows->items(),
            'pagination' => [
                'current_page' => $rows->currentPage(),
                'last_page' => $rows->lastPage(),
                'total' => $rows->total(),
                'per_page' => $rows->perPage(),
            ]
        ]);
    }

    // ✅ Normal view = uploads list
    $uploads = AutomationRawLeadUpload::where('organization_id', $orgId)
        ->orderBy('id', 'DESC')
        ->paginate(20);

    return view('FrontEnd.automation.raw_leads.index', compact('uploads'));
}

	public function uploadx(Request $request)
	{
		$request->validate([
			'file' => 'required|file|mimes:csv,txt|max:51200'
		]);

		$user  = Sentinel::check();
		$orgId = registeruser($user->id)->organization ?? null;

		$file = $request->file('file');

		$fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
		$path = $file->storeAs('raw-leads', $fileName);

		$upload = AutomationRawLeadUpload::create([
			'organization_id' => $orgId,
			'user_id'         => $user->id,
			'file_name'       => $fileName,
			'file_path'       => $path,
			'status'          => 'pending',
			'total_rows'      => 0,
			'valid_rows'      => 0,
			'duplicate_rows'  => 0,
			'failed_rows'     => 0,
		]);

		// background job
		ProcessRawLeadUploadJob::dispatch($upload->id);

		return back()->with('success', 'File uploaded successfully. Processing started.');
	}



private function detectCsvDelimiter($filePath)
{
    if (!file_exists($filePath)) {
        return ',';
    }

    $delimiters = [',', ';', "\t", '|'];

    $handle = fopen($filePath, "r");
    if (!$handle) return ',';

    $firstLine = fgets($handle);
    fclose($handle);

    if (!$firstLine) return ',';

    $bestDelimiter = ',';
    $maxFields = 0;

    foreach ($delimiters as $delimiter) {
        $fields = str_getcsv($firstLine, $delimiter);
        if (count($fields) > $maxFields) {
            $maxFields = count($fields);
            $bestDelimiter = $delimiter;
        }
    }

    return $bestDelimiter;
}



public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:csv,txt|max:51200'
    ]);

    $user  = Sentinel::check();
    $orgId = registeruser($user->id)->organization ?? null;

    $file = $request->file('file');

    $fileName = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
	// force local storage
	$path = $file->storeAs('raw-leads', $fileName, 'local');
 
	// real full path
	$fullPath = Storage::disk('local')->path($path);

   

    // ✅ Detect delimiter
    $delimiter = $this->detectCsvDelimiter($fullPath);

    // ✅ Count rows
    $totalRows = 0;

    if (file_exists($fullPath)) {
        $handle = fopen($fullPath, "r");
        if ($handle !== false) {

            // header skip
            fgetcsv($handle, 0, $delimiter);

            while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                if (is_array($data) && count(array_filter($data)) > 0) {
                    $totalRows++;
                }
            }

            fclose($handle);
        }
    }

    AutomationRawLeadUpload::create([
        'organization_id' => $orgId,
        'user_id'         => $user->id,
        'file_name'       => $fileName,
        'file_path'       => $path,
        'status'          => 'pending',
        'total_rows'      => $totalRows,
        'valid_rows'      => 0,
        'duplicate_rows'  => 0,
        'failed_rows'     => 0,
    ]);

    return back()->with('success', "File uploaded. Total rows: {$totalRows}. Processing cron se hogi.");
}




public function uploadsList()
{
    $user  = Sentinel::check();
    $orgId = $this->getOrgId();

    $uploads = AutomationRawLeadUpload::where('organization_id', $orgId)
        ->orderBy('id', 'DESC')
        ->paginate(20);

    return view('FrontEnd.automation.raw_leads.uploads', compact('uploads'));
}


	public function viewUpload(Request $request, $uploadId)
	{
		$user  = Sentinel::check();
		$orgId = registeruser($user->id)->organization ?? null;

		 $upload = AutomationRawLeadUpload::where('organization_id', $orgId)
        ->where('id', $uploadId)
        ->firstOrFail();

    // ✅ same AJAX listing page open (index.blade.php)
    return view('FrontEnd.automation.raw_leads.index', compact('upload'));
	}

    // -------------------- BULK IVR SEND --------------------
    // Bulk IVR send trigger (integration placeholder)
    public function sendIvr(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $user = Sentinel::check();
        $orgId = registeruser($user->id)->organization ?? null;

        $ids = $request->input('ids', []);
        $rows = AutomationRawLead::whereIn('id', $ids)->get();

        $sentCount = 0;
        $metaRows = [];

        foreach ($rows as $row) {
            // → Place where actual IVR provider/send-call logic will be placed.
            // Example: IvrService::sendCall($row->mobile, [...]);

            // mark status and save provider meta
            $row->status = 'ivr_sent';
            // optionally store provider_request_id etc:
            // $row->ivr_request_meta = json_encode(['provider' => 'exotel', 'request_id' => $someId]);
            $row->save();

            $sentCount++;
            $metaRows[] = ['raw_lead_id' => $row->id, 'mobile' => $row->mobile];
        }

        // LOG: bulk IVR send
        $this->logAutomationActivity(
            null,
            $user->id ?? null,
            $orgId,
            'raw_lead.ivr_sent',
            [
                'count' => $sentCount,
                'rows'  => $metaRows,
                'note'  => 'IVR bulk trigger (provider call not implemented here)'
            ]
        );

        return response()->json([
            'status'  => 'success',
            'message' => 'IVR triggered for selected records.',
            'count'   => $sentCount
        ]);
    }


    // -------------------- IVR CALLBACK --------------------
    // IVR provider will call this endpoint with DTMF / mobile / other data
    public function ivrCallback(Request $request)
    {
        // Map provider payload to our standard fields using your mapper (you already had this)
        $mapped = \App\Services\IvrPayloadMapper::mapCallback($request, 'exotel');

        $mobile = $mapped['mobile'] ?? null;
        $dtmf   = (string)($mapped['dtmf'] ?? null);
        $providerRaw = $mapped['raw'] ?? null; // raw payload for debug

        if (!$mobile || !$dtmf) {
            return response()->json(['status' => 'error', 'message' => 'Missing params'], 400);
        }

        // pick latest pending/ivr_sent/raw record for this mobile
        $rawLead = AutomationRawLead::where('mobile', $mobile)
            ->whereIn('status', ['pending', 'whatsapp_sent', 'ivr_sent'])
            ->orderBy('id', 'DESC')
            ->first();

        if (!$rawLead) {
            // log unknown callback for auditing
            $this->logAutomationActivity(
                null,
                null,
                null,
                'raw_lead.ivr_callback.unknown',
                ['mobile' => $mobile, 'dtmf' => $dtmf, 'provider_payload' => $providerRaw]
            );

            return response()->json(['status' => 'error', 'message' => 'Raw lead not found'], 404);
        }

        $user = Sentinel::check();
        $orgId = registeruser($user->id)->organization ?? $rawLead->organization_id ?? null;

        // always save callback meta
        $rawLead->ivr_response = $dtmf;
        $rawLead->confirm_time = now();

        // LOG: raw callback received (provider raw)
        $this->logAutomationActivity(
            $rawLead->id,
            $user->id ?? null,
            $orgId,
            'raw_lead.ivr_callback_received',
            [
                'raw_lead_id' => $rawLead->id,
                'mobile' => $mobile,
                'dtmf' => $dtmf,
                'provider_payload' => $providerRaw
            ]
        );

        if ($dtmf === '1') {
            // ✅ Confirmed -> convert to lead (if not already converted)
            $rawLead->status = 'confirmed';

            if (!$rawLead->lead_created_status) {
                DB::transaction(function () use ($rawLead, $user, $orgId) {

                    // Duplicate check (same mobile + same course)
                    $existing = Lead::where('mobile', $rawLead->mobile)
                        ->where('course', $rawLead->course)
                        ->first();

                    if ($existing) {
                        // Duplicate found -> mark raw as duplicate, increment weight
                        $rawLead->status = 'duplicate';
                        $rawLead->duplicate_of = $existing->id;
                        $rawLead->lead_created_status = 0;
                        $rawLead->lead_created_time = null;
                        $rawLead->save();

                        // Increase lead_weight_count on existing lead if column exists
                        try {
                            $existing->increment('lead_weight_count');
                        } catch (\Exception $e) {
                            // ignore if column missing
                        }

                        // LOG duplicate
                        $this->logAutomationActivity(
                            $rawLead->id,
                            $user->id ?? null,
                            $orgId,
                            'lead.duplicate.detected',
                            [
                                'raw_lead_id' => $rawLead->id,
                                'existing_lead_id' => $existing->id,
                                'mobile' => $rawLead->mobile,
                                'course' => $rawLead->course
                            ]
                        );

                        // Also create lead_activity_log entry (mapping to existing lead)
                        $this->logLeadActivity(
                            $existing->id,
                            $user->id ?? null,
                            $orgId,
                            'lead.duplicate.incremented',
                            ['raw_lead_id' => $rawLead->id]
                        );

                    } else {
                        // Create new lead from raw
                        $lead = new Lead();
                        $lead->name     = $rawLead->name;
                        $lead->mobile   = $rawLead->mobile;
                        $lead->course   = $rawLead->course;
                        $lead->city     = $rawLead->city;
                        $lead->campaign = $rawLead->campaign;
                        $lead->source   = $rawLead->source;
                        $lead->status   = 'new';
                        // optionally set organization_id if schema has it
                        if (isset($rawLead->organization_id)) {
                            $lead->organization_id = $rawLead->organization_id;
                        }
                        $lead->save();

                        // mark raw as converted
                        $rawLead->lead_created_status = 1;
                        $rawLead->lead_created_time = now();
                        $rawLead->status = 'converted';
                        $rawLead->converted_lead_id = $lead->id ?? null;
                        $rawLead->save();

                        // LOG: lead.created.from_raw
                        $this->logAutomationActivity(
                            $rawLead->id,
                            $user->id ?? null,
                            $orgId,
                            'lead.created.from_raw',
                            [
                                'raw_lead_id' => $rawLead->id,
                                'lead_id' => $lead->id
                            ]
                        );

                        // lead activity log
                        $this->logLeadActivity(
                            $lead->id,
                            $user->id ?? null,
                            $orgId,
                            'lead.created.from_raw',
                            ['raw_lead_id' => $rawLead->id]
                        );

                        // Optionally: push to auto-assign queue here
                        // dispatch(new \App\Jobs\AutoAssignLeadJob($lead));
                        $this->logAutomationActivity(
                            $rawLead->id,
                            $user->id ?? null,
                            $orgId,
                            'lead.auto_assign_enqueued',
                            ['lead_id' => $lead->id]
                        );
                    }
                });
            }
        } elseif ($dtmf === '2') {
            // Rejected
            $rawLead->status = 'rejected';
            $rawLead->save();

            $this->logAutomationActivity(
                $rawLead->id,
                $user->id ?? null,
                $orgId,
                'raw_lead.ivr_rejected',
                ['raw_lead_id' => $rawLead->id]
            );
        } else {
            // Invalid/no input
            $rawLead->status = 'ivr_failed';
            $rawLead->save();

            $this->logAutomationActivity(
                $rawLead->id,
                $user->id ?? null,
                $orgId,
                'raw_lead.ivr_failed',
                ['raw_lead_id' => $rawLead->id, 'dtmf' => $dtmf]
            );
        }

        return response()->json(['status' => 'ok']);
    }


    // -------------------- MANUAL CONVERT --------------------
    // Manual convert raw lead to main lead via UI button
    public function manualConvert($id)
    {
        $user = Sentinel::check();
        $orgId = registeruser($user->id)->organization ?? null;

        $rawLead = AutomationRawLead::findOrFail($id);

        if ($rawLead->lead_created_status) {
            return response()->json(['status' => 'error', 'message' => 'Lead already created.'], 400);
        }

        DB::transaction(function () use ($rawLead, $user, $orgId) {
            // Duplicate check before manual convert
            $existing = Lead::where('mobile', $rawLead->mobile)
                ->where('course', $rawLead->course)
                ->first();

            if ($existing) {
                // mark duplicate
                $rawLead->status = 'duplicate';
                $rawLead->duplicate_of = $existing->id;
                $rawLead->save();

                // log duplicate
                $this->logAutomationActivity(
                    $rawLead->id,
                    $user->id ?? null,
                    $orgId,
                    'lead.duplicate.detected.manual',
                    [
                        'raw_lead_id' => $rawLead->id,
                        'existing_lead_id' => $existing->id
                    ]
                );

                $this->logLeadActivity(
                    $existing->id,
                    $user->id ?? null,
                    $orgId,
                    'lead.duplicate.marked.manual',
                    ['raw_lead_id' => $rawLead->id]
                );

                return;
            }

            // create new lead
            $lead = new Lead();
            $lead->name     = $rawLead->name;
            $lead->mobile   = $rawLead->mobile;
            $lead->course   = $rawLead->course;
            $lead->city     = $rawLead->city;
            $lead->campaign = $rawLead->campaign;
            $lead->source   = $rawLead->source;
            $lead->status   = 'new';
            if (isset($rawLead->organization_id)) {
                $lead->organization_id = $rawLead->organization_id;
            }
            $lead->save();

            // update raw lead
            $rawLead->status = 'converted';
            $rawLead->lead_created_status = 1;
            $rawLead->lead_created_time = now();
            $rawLead->converted_lead_id = $lead->id ?? null;
            $rawLead->save();

            // logging
            $this->logAutomationActivity(
                $rawLead->id,
                $user->id ?? null,
                $orgId,
                'raw_lead.manual_converted',
                ['raw_lead_id' => $rawLead->id, 'lead_id' => $lead->id]
            );

            $this->logLeadActivity(
                $lead->id,
                $user->id ?? null,
                $orgId,
                'lead.created.from_raw.manual',
                ['raw_lead_id' => $rawLead->id]
            );
        });

        return response()->json(['status' => 'success', 'message' => 'Lead converted successfully.']);
    }


    // -------------------- IMPORT (placeholder) --------------------
    public function import(Request $request)
    {
        $user = Sentinel::check();
        $orgId = registeruser($user->id)->organization ?? null;

        // implement your CSV handling & mapping here
        // For each imported row → create AutomationRawLead record
        // and log raw_lead.imported

        // Example placeholder:
        $importedCount = 0;
        // ... your import logic fills $importedCount and $importedRows

        $this->logAutomationActivity(null, $user->id ?? null, $orgId, 'raw_lead.imported', [
            'count' => $importedCount
        ]);

        return response()->json(['status' => 'success', 'imported' => $importedCount]);
    }


    // ------------------------------------------------------------------
    // 🔥 UNIVERSAL LOG HELPERS (create immutable logs)
    // ------------------------------------------------------------------

    /**
     * Generic automation log (for raw leads related events)
     * Writes to user_activity_logs and org_activity_logs.
     *
     * @param int|null $rawLeadId
     * @param int|null $userId
     * @param int|null $orgId
     * @param string $event
     * @param array $meta
     * @return void
     */
    private function logAutomationActivity($rawLeadId, $userId, $orgId, $event, $meta = [])
    {
        $ip = request()->ip();
        $agent = request()->header('User-Agent');

        // Normalize meta
        $meta['raw_lead_id'] = $rawLeadId;
        $meta_json = json_encode($meta);

        try {
            // USER LOG
            UserActivityLog::create([
                'user_id'        => $userId,
                'organization_id'=> $orgId,
                'event'          => $event,
                'meta_json'      => $meta_json,
                'ip_address'     => $ip,
                'user_agent'     => $agent,
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // swallow so logs don't break main flow
        }

        try {
            // ORG LOG
            OrgActivityLog::create([
                'organization_id'=> $orgId,
                'event'          => $event,
                'meta_json'      => $meta_json,
                'ip_address'     => $ip,
                'user_agent'     => $agent,
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // ignore
        }
    }

    /**
     * Lead specific activity log (goes to lead_activity_logs)
     */
    private function logLeadActivity($leadId, $userId, $orgId, $event, $meta = [])
    {
        $ip = request()->ip();
        $agent = request()->header('User-Agent');

        $meta_json = json_encode($meta);

        try {
            LeadActivityLog::create([
                'lead_id'        => $leadId,
                'user_id'        => $userId,
                'organization_id'=> $orgId,
                'event'          => $event,
                'meta_json'      => $meta_json,
                'ip_address'     => $ip,
                'user_agent'     => $agent,
                'created_at'     => now(),
            ]);
        } catch (\Exception $e) {
            // ignore
        }
    }
}
