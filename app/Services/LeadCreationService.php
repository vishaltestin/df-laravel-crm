<?php

namespace App\Services;

use App\Lead;
use App\LeadApplication;
use App\LeadVerification;
use App\LeadCommunicationLog;
use App\LeadSource;
use Illuminate\Support\Facades\DB;

class LeadCreationService
{
    public static function createFromBulk($upload, $data)
    {
        DB::beginTransaction();

        try {

            // Duplicate check (mobile + programme + org)
            $existing = Lead::where('organization_id', $upload->organization_id)
                ->where('mobile', $data['mobile'])
                ->where('programme_id', $upload->program)
                ->first();

            if ($existing) {

                $existing->increment('lead_weight_count');

                DB::commit();
                return 'duplicate';
            }

            $leadPassword = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            $lead = new Lead();
            $lead->organization_id = $upload->organization_id;
            $lead->created_by      = $upload->user_id;

            $lead->name   = $data['name'] ?? null;
            $lead->mobile = $data['mobile'];
            $lead->email  = $data['email'] ?? null;

            $lead->city    = $data['city'] ?? null;
            $lead->state   = $data['state'] ?? null;
            $lead->country = 80;

            $lead->institute_id = $upload->institute;
            $lead->programme_id = $upload->program;
            $lead->faculty_id   = $upload->course;

            $lead->lead_assign_to = $upload->assign_user ?? $upload->user_id;
            $lead->password       = $leadPassword;

            $lead->lead_status_id = 1;
            $lead->pipeline_id    = 1;
            $lead->stage_id       = 1;

            $lead->lead_registration_date = now();

            $lead->save();

            // Application
            LeadApplication::create([
                'lead_id' => $lead->id,
                'application_form' => $upload->program,
                'application_score' => 0,
                'payment_status' => 'pending'
            ]);

            // Verification
            LeadVerification::create([
                'lead_id' => $lead->id
            ]);

            // Communication Log
            LeadCommunicationLog::create([
                'lead_id' => $lead->id,
                'channel' => 'bulk',
                'direction' => 'inbound',
                'purpose' => 'Lead imported via bulk upload'
            ]);

            // Activity Log (your helper)
            logLeadActivity(
                $upload->organization_id,
                $lead->id,
                $upload->user_id,
                'lead_imported',
                "Lead imported via bulk upload",
                [
                    'mobile' => $lead->mobile
                ]
            );

            DB::commit();
            return 'created';

        } catch (\Throwable $e) {

            DB::rollBack();
            return 'failed';
        }
    }
}