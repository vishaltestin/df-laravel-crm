<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Lead;
use App\LeadSource;
use App\LeadIntegration;
use App\LeadIntegrationLog;
use App\Services\FacebookLeadService;
use App\Services\GoogleLeadService;
class LeadWebhookController extends Controller
{
    public function handle(Request $request, $provider)
    {
        $payload = $request->all();

        // ------------------------------------
        // 1️⃣ Find ACTIVE integration
        // ------------------------------------
        $integration = LeadIntegration::where('provider', $provider)
            ->where('is_enabled', 1)
            ->first();

        if (!$integration) {
            return response()->json(['status' => 'integration_disabled'], 200);
        }

        $organizationId = $integration->organization_id;

        // ------------------------------------
        // 2️⃣ Get Lead Source from table
        // ------------------------------------
        $source = LeadSource::where('organization_id', $organizationId)
            ->whereRaw('LOWER(name) = ?', [strtolower($provider)])
            ->where('status', 1)
            ->first();

        if (!$source) {
            return response()->json(['status' => 'source_not_found'], 200);
        }

        // ------------------------------------
        // 3️⃣ Normalize provider payload
        // ------------------------------------
        $leadData = $this->extractLeadData($provider, $payload);

        if (!$leadData || empty($leadData['mobile'])) {
            $this->log($organizationId, $provider, null, 'invalid_payload', $payload);
            return response()->json(['status' => 'invalid_payload'], 200);
        }

        // ------------------------------------
        // 4️⃣ Duplicate check (mobile)
        // ------------------------------------
        $existing = Lead::where('organization_id', $organizationId)
            ->where('mobile', $leadData['mobile'])
            ->first();

        if ($existing) {
            $this->log($organizationId, $provider, $existing->id, 'duplicate', $payload);
            return response()->json(['status' => 'duplicate'], 200);
        }

        // ------------------------------------
        // 5️⃣ Insert Lead
        // ------------------------------------
        $lead = Lead::create([
            'organization_id' => $organizationId,
            'lead_source_id'  => $source->id,
            'name'            => $leadData['name'],
            'email'           => $leadData['email'],
            'mobile'          => $leadData['mobile'],
            'created_at'      => now(),
        ]);

        // ------------------------------------
        // 6️⃣ Update source billing + count
        // ------------------------------------
        $source->increment('lead_count');

        if ($source->billing_setup_amount > 0) {
            $source->increment(
                'total_amount',
                $source->billing_setup_amount
            );
        }

        // ------------------------------------
        // 7️⃣ Log success
        // ------------------------------------
        $this->log($organizationId, $provider, $lead->id, 'success', $payload);

        return response()->json(['status' => 'ok'], 200);
    }

    // =====================================================
    // Provider-wise payload mapping
    // =====================================================
    private function extractLeadData($provider, $payload)
    {
        switch (strtolower($provider)) {

            case 'facebook':
                return [
                    'name'   => $payload['name']   ?? '',
                    'email'  => $payload['email']  ?? '',
                    'mobile' => $payload['phone']  ?? '',
                ];

            case 'google':
                return [
                    'name'   => $payload['full_name'] ?? '',
                    'email'  => $payload['email']     ?? '',
                    'mobile' => $payload['phone']     ?? '',
                ];

            case 'instagram':
                return [
                    'name'   => $payload['name']   ?? '',
                    'email'  => $payload['email']  ?? '',
                    'mobile' => $payload['mobile'] ?? '',
                ];
        }

        return null;
    }

    // =====================================================
    // Central logging
    // =====================================================
    private function log($orgId, $provider, $leadId, $status, $payload)
    {
		
		
        LeadIntegrationLog::create([
            'organization_id' => $orgId,
            'provider'        => $provider,
            'lead_id'         => $leadId,
            'status'          => $status,
            'payload'         => json_encode($payload),
        ]);
    }

  // -----------------------------------------------------
    // FACEBOOK / META VERIFY LOGIC
    // -----------------------------------------------------
    private function verifyWebhook(Request $request, $provider)
    {
        $mode      = $request->get('hub_mode');
        $token     = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($mode !== 'subscribe' || !$token) {
            return response('Invalid request', 403);
        }

        // 🔍 ACTIVE integration
        $integration = LeadIntegration::where('provider', $provider)
            ->where('is_enabled', 1)
            ->first();

        if (!$integration) {
            return response('Integration disabled', 403);
        }

        // 🔐 Fetch verify token from DB
        $cred = LeadIntegrationCredential::where([
            'organization_id' => $integration->organization_id,
            'provider'        => $provider,
            'key'             => 'verify_token',
        ])->first();

        if (!$cred) {
            return response('Verify token not configured', 403);
        }

        // 🔓 Safe decrypt
        try {
            $dbToken = Crypt::decryptString($cred->value);
        } catch (\Exception $e) {
            $dbToken = $cred->value; // fallback (plain)
        }

        // ✅ MATCH
        if ($token === $dbToken) {
            return response($challenge, 200);
        }

        return response('Token mismatch', 403);
    }

    // -----------------------------------------------------
    // REAL LEAD PROCESSING
    // -----------------------------------------------------
  
private function processLead(Request $request, $provider)
{
    $payload = $request->all();

    // =====================================================
    // 1️⃣ Active integration
    // =====================================================
    $integration = LeadIntegration::where('provider', $provider)
        ->where('is_enabled', 1)
        ->first();

    if (!$integration) {
        return response()->json(['status' => 'integration_disabled'], 200);
    }

    $organizationId = $integration->organization_id;

    // =====================================================
    // 2️⃣ Source lookup
    // =====================================================
    $source = LeadSource::where('organization_id', $organizationId)
        ->whereRaw('LOWER(name) = ?', [strtolower($provider)])
        ->where('status', 1)
        ->first();

    if (!$source) {
        return response()->json(['status' => 'source_not_found'], 200);
    }

    // =====================================================
    // 3️⃣ PROVIDER-SPECIFIC HANDLING
    // =====================================================
    switch (strtolower($provider)) {

        // ---------------- FACEBOOK ----------------
        case 'facebook':

            $leadgenId = $payload['entry'][0]['changes'][0]['value']['leadgen_id'] ?? null;

            if (!$leadgenId) {
                $this->log($organizationId,'facebook',null,'leadgen_id_missing',$payload);
                return response()->json(['status'=>'ignored'],200);
            }

            $leadData = FacebookLeadService::fetchLead(
                $organizationId,
                $leadgenId
            );

            if (!$leadData || empty($leadData['mobile'])) {
                $this->log($organizationId,'facebook',null,'fetch_failed',$payload);
                return response()->json(['status'=>'failed'],200);
            }

            break;

        // ---------------- GOOGLE ----------------
        case 'google':

            /*
             Google Ads usually sends:
             resource_name / lead_form_submission
             Then we fetch details via Google Ads API
            */

            $leadData = GoogleLeadService::extractFromWebhook(
                $organizationId,
                $payload
            );

            if (!$leadData || empty($leadData['mobile'])) {
                $this->log($organizationId,'google',null,'invalid_payload',$payload);
                return response()->json(['status'=>'invalid_payload'],200);
            }

            break;

        // ---------------- INSTAGRAM ----------------
        case 'instagram':

            /*
             Instagram Lead Ads = Facebook Infra
             Mostly same leadgen_id flow
            */

            $leadgenId = $payload['entry'][0]['changes'][0]['value']['leadgen_id'] ?? null;

            if (!$leadgenId) {
                $this->log($organizationId,'instagram',null,'leadgen_id_missing',$payload);
                return response()->json(['status'=>'ignored'],200);
            }

            $leadData = FacebookLeadService::fetchLead(
                $organizationId,
                $leadgenId
            );

            if (!$leadData || empty($leadData['mobile'])) {
                $this->log($organizationId,'instagram',null,'fetch_failed',$payload);
                return response()->json(['status'=>'failed'],200);
            }

            break;

        default:
            return response()->json(['status'=>'unknown_provider'],200);
    }

    // =====================================================
    // 4️⃣ DUPLICATE CHECK
    // =====================================================
    $existing = Lead::where('organization_id',$organizationId)
        ->where('mobile',$leadData['mobile'])
        ->first();

    if ($existing) {
        $this->log($organizationId,$provider,$existing->id,'duplicate',$payload);
        return response()->json(['status'=>'duplicate'],200);
    }

    // =====================================================
    // 5️⃣ INSERT LEAD
    // =====================================================
    $lead = Lead::create([
        'organization_id' => $organizationId,
        'lead_source_id'  => $source->id,
        'name'            => $leadData['name'] ?? '',
        'email'           => $leadData['email'] ?? '',
        'mobile'          => $leadData['mobile'],
        'created_at'      => now(),
    ]);

    // =====================================================
    // 6️⃣ BILLING UPDATE
    // =====================================================
    $source->increment('lead_count');

    if ($source->billing_setup_amount > 0) {
        $source->increment('total_amount', $source->billing_setup_amount);
    }

    // =====================================================
    // 7️⃣ SUCCESS LOG
    // =====================================================
    $this->log($organizationId,$provider,$lead->id,'success',$payload);

    return response()->json(['status'=>'ok'],200);
}
}
