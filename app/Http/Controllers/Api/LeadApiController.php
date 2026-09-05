<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Models
use App\Lead;
use App\LeadApplication;
use App\LeadVerification;
use App\LeadCommunicationLog;
use App\LeadSource;

class LeadApiController extends Controller
{
    public function store(Request $req)
    {
        $orgId     = $req->organization_id;
		$createdBy = $req->created_by;
      
	  if (!$orgId) {
            return response()->json([
                'status' => false,
                'message' => 'Organization ID required'
            ]);
        }

					/* -----------------------------
			 | VALIDATION
			 ----------------------------- */
			$rules = [
				'organization_id'     => 'required|integer',
				'created_by'          => 'required|integer',
				'name'                => 'required|string|max:150',
				'mobile'              => 'required|string|max:20',
				'application_form_id' => 'required|integer',
				'lead_marketing_event'=> 'required|integer',
			];

			if ($req->upload_via === 'email') {
				$rules['email'] = 'required|email';
			}

			$validator = \Validator::make($req->all(), $rules);

			if ($validator->fails()) {
				return response()->json([
					'status'  => false,
					'message' => $validator->errors()->first()
				]);
			}

        DB::beginTransaction();

        try {

           


								/* ----------------------------------
								 | PIPELINE DEFAULT DETECTION
								 ---------------------------------- */

								// 1️⃣ Get Pipeline
								$pipeline = DB::table('pipelines')
									->where('organization_id', $orgId)
									->orderByDesc('is_default')
									->first();

								if (!$pipeline) {
									$pipeline = DB::table('pipelines')->first();
								}

								$pipelineId = $pipeline?->id ?? 1;

								// 2️⃣ Get First Stage (minimum sort_order)
								$stage = DB::table('pipeline_stages')
									->where('lead_pipeline_id', $pipelineId)
									->orderBy('sort_order')
									->first();

								$stageId = $stage?->id ?? null;


                                //piplecheck
                                $verifiedStage = DB::table('pipeline_stages')
                                    ->where('lead_pipeline_id', $pipelineId)
                                    ->where('code', 'verified')
                                    ->first();

                                $verifiedStageId  = $verifiedStage?->id;

								/* ----------------------------------
								 | LEAD STATUS DEFAULT DETECTION
								 ---------------------------------- */

								// 3️⃣ Get Lead Status (org specific first, else NULL)
								$status = DB::table('lead_statuses')
									->where(function ($q) use ($orgId) {
										$q->where('organization_id', $orgId)
										  ->orWhereNull('organization_id');
									})
									->where('is_active', 1)
									->orderByRaw("organization_id IS NULL")
									->orderBy('sort_order')
									->first();

								$leadStatusId = $status?->id ?? null;


								// 4️⃣ Get Sub Status (based on selected status)
								$subStatus = DB::table('lead_sub_statuses')
									->where('lead_status_id', $leadStatusId)
									->where(function ($q) use ($orgId) {
										$q->where('organization_id', $orgId)
										  ->orWhereNull('organization_id');
									})
									->where('is_active', 1)
									->orderByRaw("organization_id IS NULL")
									->orderBy('sort_order')
									->first();

								$leadSubStatusId = $subStatus?->id ?? null;

    $smsVerified = DB::table('otp_verifications')
    ->where('organization_id', $orgId)
    ->where('type', 'mobile')
    ->where('recipient', $req->mobile)
    ->where('is_verified', 1)
    ->exists();

 
     $emailVerified = DB::table('otp_verifications')
        ->where('organization_id', $orgId)
        ->where('type', 'email')
        ->where('recipient', $req->email)
        ->where('is_verified', 1)
        ->exists();
 


		   /* -----------------------------
             | CREATE LEAD
             ----------------------------- */
            $lead = new Lead();
            $leadPassword = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            $lead->organization_id = $orgId;
            $lead->created_by      = $createdBy;

            $lead->name   = $req->name;
            $lead->mobile = $req->mobile;
            $lead->email  = $req->email;

            $lead->state   = $req->state ?? null;
            $lead->city    = $req->city ?? null;
            $lead->country = $req->country ?? 80;

            $lead->lead_assign_to = $req->lead_assign_to ?? $createdBy;

            $lead->institute_id = $req->institute_id ?? null;
            $lead->programme_id = $req->programme_id ?? null;
            $lead->faculty_id   = $req->faculty_id ?? null;

            $lead->password = $leadPassword;

            $lead->lead_source_id   = $req->source ?? null;
            $lead->lead_campaign_id = $req->campaign ?? null;
            $lead->type_id          = $req->medium ?? null;
            $lead->application_form_id = $req->application_form_id ?? null;
			$lead->lead_marketing_event = $req->lead_marketing_event;
            $lead->pipeline_id        = $pipelineId;

            if($smsVerified || $emailVerified){
                $lead->stage_id = $verifiedStageId;
            }else{
                $lead->stage_id = $stageId;
            }

            $lead->lead_status_id     = $leadStatusId;
			$lead->lead_sub_status_id = $leadSubStatusId;
			$lead->extra_fields_json = json_encode([
                'qualification' => $req->qualification,
                'upload_via'    => $req->upload_via,
            ]);

            $lead->lead_registration_date = now();
            $lead->save();

            /* ----------------------------------
             | CHANNEL + SOURCE DETECTION
             ---------------------------------- */
            $channel = match ($req->upload_via ?? null) {
                'email'       => 'email',
                'whatsapp'    => 'whatsapp',
                'bulk'        => 'bulk',
                'google_ads'  => 'google_ads',
                'partner_api' => 'partner_api',
                default       => 'mobile',
            };

            $sourceType = $req->source_type ?? 'api';

            /* ----------------------------------
             | MASTER CREATION LOG
             ---------------------------------- */
            DB::table('lead_creation_logs')->insert([
                'organization_id' => $orgId,
                'lead_id'         => $lead->id,
                'created_by'      => $createdBy,
                'source_type'     => $sourceType,
                'channel'         => $channel,
                'upload_id'       => $req->upload_id ?? null,
                'ip_address'      => $req->ip(),
                'user_agent'      => $req->header('User-Agent'),
                'remarks'         => "Lead created via {$sourceType} using {$channel}",
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            /* ----------------------------------
             | APPLICATION
             ---------------------------------- */
            if ($req->application_form_id) {

                DB::table('application_form_responses')->updateOrInsert(
                    [
                        'lead_form_id' => $req->application_form_id,
                        'lead_id'      => $lead->id,
                    ],
                    [
                        'organization_id' => $orgId,
                        'assigned_to'     => $lead->lead_assign_to,
                        'response_json'   => json_encode([]),
                        'fill_percentage' => 0,
                        'payment_status'  => 0,
                        'current_step'    => 1,
                        'status'          => 'draft',
                        'updated_at'      => now(),
                        'created_at'      => now(),
                    ]
                );

                LeadApplication::updateOrCreate(
                    ['lead_id' => $lead->id],
                    [
                        'application_form'  => $req->application_form_id,
                        'application_score' => 0,
                        'payment_status'    => 'pending',
                    ]
                );
            }

            /* ----------------------------------
             | VERIFICATION
             ---------------------------------- */
          LeadVerification::create([
                'lead_id'        => $lead->id,
                'sms_verified'   => $smsVerified ? 1 : 0,
                'email_verified' => $emailVerified ? 1 : 0,
                'verified_at'    => ($smsVerified || $emailVerified) ? now() : null,
            ]);

            /* ----------------------------------
             | COMMUNICATION LOG
             ---------------------------------- */
            LeadCommunicationLog::create([
                'lead_id'   => $lead->id,
                'channel'   => $channel,
                'direction' => 'inbound',
                'purpose'   => "New Lead Created via {$channel}",
            ]);

            /* ----------------------------------
             | SOURCE COUNT
             ---------------------------------- */
            if ($lead->lead_source_id) {
                LeadSource::where('id', $lead->lead_source_id)
                    ->where('organization_id', $orgId)
                    ->increment('lead_count');
            }

            /* ----------------------------------
             | LEAD ACTIVITY LOG
             ---------------------------------- */
            logLeadActivity(
                $orgId,
                $lead->id,
                $createdBy,
                'lead_created',
                "Lead created via API",
                [
                    'lead_name' => $lead->name,
                    'mobile'    => $lead->mobile,
                    'via'       => $channel,
                    'created_at'=> now()->toDateTimeString(),
                ]
            );

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Lead created successfully',
                'lead_id' => $lead->id,
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}