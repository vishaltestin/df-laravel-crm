<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\AutomationRawLeadUpload;
use App\AutomationRawLead;

class ProcessRawLeadUploads extends Command
{
    protected $signature = 'raw-leads:process {--limit=1}';
    protected $description = 'Process pending raw lead uploads and insert into automation_raw_leads';

    public function handle()
    {
        $limit = (int) $this->option('limit');

        $uploads = AutomationRawLeadUpload::where('status', 'pending')
            ->orderBy('id', 'ASC')
            ->limit($limit)
            ->get();

        if ($uploads->count() == 0) {
            $this->info("No pending uploads found.");
            return 0;
        }

        foreach ($uploads as $upload) {
            $this->processOneUpload($upload);
        }

        return 0;
    }

    private function processOneUpload($upload)
    {
        try {

            $upload->status = 'processing';
            $upload->started_at = now();
            $upload->error_message = null;
            $upload->save();

            // full path
            $filePath = public_path($upload->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception("File not found: " . $filePath);
            }

            // detect delimiter
            $delimiter = $this->detectCsvDelimiter($filePath);

            $handle = fopen($filePath, "r");
            if (!$handle) {
                throw new \Exception("Unable to open file.");
            }

            // header read
            $header = fgetcsv($handle, 0, $delimiter);

            $total = 0;
            $valid = 0;
            $dup   = 0;
            $fail  = 0;

            $chunk = [];

            while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {

                $total++;

                // blank row skip
                if (!is_array($row) || count(array_filter($row)) == 0) {
                    $fail++;
                    continue;
                }

                // column mapping (simple)
                $name     = trim($row[0] ?? '');
                $mobile   = trim($row[1] ?? '');
                $course   = trim($row[2] ?? '');
                $city     = trim($row[3] ?? '');
                $campaign = trim($row[4] ?? '');
                $source   = trim($row[5] ?? '');

                // mobile validation
                if (!$mobile || strlen(preg_replace('/\D/', '', $mobile)) < 10) {
                    $fail++;
                    continue;
                }

                // duplicate check in automation_raw_leads (same org + mobile + course)
                $exists = AutomationRawLead::where('organization_id', $upload->organization_id)
                    ->where('mobile', $mobile)
                    ->where('course', $course)
                    ->exists();

                if ($exists) {
                    $dup++;
                    continue;
                }

                $chunk[] = [
                    'upload_id' => $upload->id,
                    'organization_id' => $upload->organization_id,
                    'name' => $name,
                    'mobile' => $mobile,
                    'course' => $course,
                    'city' => $city,
                    'campaign' => $campaign,
                    'source' => $source,
                    'status' => 'pending',
                    'lead_created_status' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $valid++;

                // insert per 500 rows
                if (count($chunk) >= 500) {
                    AutomationRawLead::insert($chunk);
                    $chunk = [];

                    // progress update
                    $upload->total_rows = $total;
                    $upload->valid_rows = $valid;
                    $upload->duplicate_rows = $dup;
                    $upload->failed_rows = $fail;
                    $upload->save();
                }
            }

            fclose($handle);

            // last chunk insert
            if (count($chunk) > 0) {
                AutomationRawLead::insert($chunk);
            }

            // final update
            $upload->total_rows = $total;
            $upload->valid_rows = $valid;
            $upload->duplicate_rows = $dup;
            $upload->failed_rows = $fail;
            $upload->status = 'completed';
            $upload->completed_at = now();
            $upload->save();

            $this->info("Upload ID {$upload->id} completed. Total={$total}, Valid={$valid}, Dup={$dup}, Fail={$fail}");

        } catch (\Exception $e) {

            $upload->status = 'failed';
            $upload->error_message = $e->getMessage();
            $upload->completed_at = now();
            $upload->save();

            $this->error("Upload ID {$upload->id} failed: " . $e->getMessage());
        }
    }

    private function detectCsvDelimiter($filePath)
    {
        $delimiters = [',', ';', "\t", '|'];

        $firstLine = '';
        $handle = fopen($filePath, "r");
        if ($handle) {
            $firstLine = fgets($handle);
            fclose($handle);
        }

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
}
