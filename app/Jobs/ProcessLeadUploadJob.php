<?php

namespace App\Jobs;

use App\LeadUpload;
use App\Services\LeadCreationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class ProcessLeadUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $uploadId;

    public function __construct($uploadId)
    {
        $this->uploadId = $uploadId;
    }

    public function handle()
    {
        $upload = LeadUpload::find($this->uploadId);
        if (!$upload || $upload->status !== 'uploaded') return;

        $upload->update([
            'status' => 'processing',
            'started_at' => now()
        ]);

        $fullPath = Storage::path($upload->file_path);
        if (!file_exists($fullPath)) {
            $upload->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_message' => 'File not found'
            ]);
            return;
        }

        $handle = fopen($fullPath, "r");
        $header = fgetcsv($handle);
        $header = array_map(fn($h) => strtolower(trim($h)), $header);

        $total = 0;
        $valid = 0;
        $dup   = 0;
        $fail  = 0;

        while (($row = fgetcsv($handle)) !== false) {

            $total++;

            $data = [];
            foreach ($header as $i => $key) {
                $data[$key] = $row[$i] ?? null;
            }

            if (empty($data['mobile'])) {
                $fail++;
                continue;
            }

            $result = LeadCreationService::createFromBulk($upload, $data);

            if ($result === 'created') $valid++;
            elseif ($result === 'duplicate') $dup++;
            else $fail++;
        }

        fclose($handle);

        $upload->update([
            'total_rows'     => $total,
            'valid_rows'     => $valid,
            'duplicate_rows' => $dup,
            'failed_rows'    => $fail,
            'status'         => 'completed',
            'completed_at'   => now()
        ]);
    }
}