<?php

namespace App\Jobs;

use App\AutomationRawLead;
use App\AutomationRawLeadUpload;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProcessRawLeadUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uploadId;

    public function __construct($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    public function handle()
    {
        $upload = AutomationRawLeadUpload::find($this->uploadId);

        if (!$upload) return;

        $upload->status = 'processing';
        $upload->started_at = now();
        $upload->save();

        $path = $upload->file_path;

        if (!Storage::exists($path)) {
            $upload->status = 'failed';
            $upload->error_message = 'File not found in storage.';
            $upload->completed_at = now();
            $upload->save();
            return;
        }

        $fullPath = Storage::path($path);

        $handle = fopen($fullPath, "r");
        if (!$handle) {
            $upload->status = 'failed';
            $upload->error_message = 'Cannot open CSV file.';
            $upload->completed_at = now();
            $upload->save();
            return;
        }

        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $upload->status = 'failed';
            $upload->error_message = 'CSV header missing.';
            $upload->completed_at = now();
            $upload->save();
            return;
        }

        // normalize header keys
        $header = array_map(function ($h) {
            return strtolower(trim($h));
        }, $header);

        $total = 0;
        $valid = 0;
        $dup   = 0;
        $fail  = 0;

        $chunk = [];
        $chunkSize = 500;

        while (($row = fgetcsv($handle)) !== false) {

            $total++;

            $data = [];
            foreach ($header as $i => $key) {
                $data[$key] = $row[$i] ?? null;
            }

            // --- MAPPING (CSV -> raw lead fields) ---
            // REQUIRED: mobile
            $mobile = $data['mobile'] ?? $data['phone'] ?? null;
            $name   = $data['name'] ?? null;
            $course = $data['course'] ?? $data['program'] ?? null;
            $city   = $data['city'] ?? null;

            if (!$mobile) {
                $fail++;
                continue;
            }

            $chunk[] = [
                'upload_id'        => $upload->id,
                'organization_id'  => $upload->organization_id,
                'name'             => $name,
                'mobile'           => $mobile,
                'course'           => $course,
                'city'             => $city,
                'status'           => 'pending',
                'created_at'       => now(),
                'updated_at'       => now(),
            ];

            if (count($chunk) >= $chunkSize) {
                $this->insertChunk($chunk, $upload, $valid, $dup);
                $chunk = [];
            }
        }

        // last chunk
        if (!empty($chunk)) {
            $this->insertChunk($chunk, $upload, $valid, $dup);
        }

        fclose($handle);

        $upload->total_rows     = $total;
        $upload->valid_rows     = $valid;
        $upload->duplicate_rows = $dup;
        $upload->failed_rows    = $fail;
        $upload->status         = 'completed';
        $upload->completed_at   = now();
        $upload->save();
    }

    private function insertChunk($chunk, $upload, &$valid, &$dup)
    {
        // Duplicate check: SAME upload में duplicate mobile ignore
        // (तुम चाहो तो org level duplicate भी कर सकते हो)

        $mobiles = array_values(array_unique(array_filter(array_column($chunk, 'mobile'))));

        $existing = AutomationRawLead::where('organization_id', $upload->organization_id)
            ->whereIn('mobile', $mobiles)
            ->pluck('mobile')
            ->toArray();

        $existingMap = array_flip($existing);

        $final = [];

        foreach ($chunk as $r) {
            if (isset($existingMap[$r['mobile']])) {
                $dup++;
                continue;
            }
            $final[] = $r;
        }

        if (!empty($final)) {
            DB::table('automation_raw_leads')->insert($final);
            $valid += count($final);
        }
    }
}
