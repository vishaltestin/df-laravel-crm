<?php

use App\User;
use App\Register;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Session;
use App\LeadSetting;
/**
 * Existing helper functions (your original code)
 */
function userRole() {
    $user = \Sentinel::check();
    return $user->roles()->first()->slug ?? null;
}


function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return 'th';
    else
        return $ends[$number % 10];
}

function xssafe($data,$encoding='UTF-8')
{
    return htmlspecialchars($data,ENT_QUOTES | ENT_HTML401,$encoding);
}

function paginate($items, $perPage = 15, $page = null, $options = [])
{
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
}

function month_date_convert($date){
    $date_year=substr($date,0,4);
    $date_month=substr($date,5,2);
    $date_day=substr($date,8,2);
    return date("jS M", mktime(0,0,0,$date_month,$date_day,$date_year));
}

function day($date){
    $date_year=substr($date,0,4);
    $date_month=substr($date,5,2);
    $date_day=substr($date,8,2);
    return date("d", mktime(0,0,0,$date_month,$date_day,$date_year));
}

function month($date){
    $date_year=substr($date,0,4);
    $date_month=substr($date,5,2);
    $date_day=substr($date,8,2);
    return date("M", mktime(0,0,0,$date_month,$date_day,$date_year));
}

function year($date){
    $date_year=substr($date,0,4);
    $date_month=substr($date,5,2);
    $date_day=substr($date,8,2);
    return date("Y", mktime(0,0,0,$date_month,$date_day,$date_year));
}

function date_day_convert($date){
    $date_year=substr($date,0,4);
    $date_month=substr($date,5,2);
    $date_day=substr($date,8,2);
    return date("jS F, l", mktime(0,0,0,$date_month,$date_day,$date_year));
}
function datec($date)
{
    if (!$date) {
        return '-';
    }

    $timestamp = strtotime($date);

    // 🔹 Agar aaj ki date hai
    if (date('Y-m-d', $timestamp) === date('Y-m-d')) {
        return 'Today ' . date('h:i A', $timestamp);
    }

    // 🔹 Purani date
    return date('jS M Y', $timestamp);
}

function date_convert($date){
    $date_year=substr($date,0,4);
    $date_month=substr($date,5,2);
    $date_day=substr($date,8,2);
    return date("F jS, Y", mktime(0,0,0,$date_month,$date_day,$date_year));
}

function datetime_convert($date){
    $date_year=substr($date,0,4);
    $date_month=substr($date,5,2);
    $date_day=substr($date,8,2);
    $date_hour = substr($date,11,2);
    $date_min = substr($date,14,2);
    $date_seconds = substr($date,17,2);

    return date("jS F Y  h:i:s A",
        mktime($date_hour,$date_min,$date_seconds,$date_month,$date_day,$date_year)
    );
}

function random_password($length = 8) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
    return substr(str_shuffle($chars), 0, $length);
}

function registeruser($id)
{
    return Register::where('user_id','=', $id)->first();
}



/**
 * 1️⃣ ORGANIZATION ACTIVITY LOG
 * Table: org_activity_logs
 */
function logOrganizationActivity(
    $organization_id,
    $user_id = null,
    $entity_type = null,
    $entity_id = null,
    $action = null,
    $description = null,
    $meta_data = null
){
    DB::table('org_activity_logs')->insert([
        'organization_id' => $organization_id,
        'user_id'         => $user_id,
        'entity_type'     => $entity_type,
        'entity_id'       => $entity_id,
        'action'          => $action,
        'description'     => $description,
        'meta_data'       => $meta_data ? json_encode($meta_data, JSON_UNESCAPED_UNICODE) : null,
        'created_at'      => now(),
    ]);
}


/**
 * 2️⃣ USER ACTIVITY LOG
 * Table: user_activity_logs
 */
function logUserActivity(
    $organization_id,
    $user_id,
    $entity_type = null,
    $entity_id = null,
    $action = null,
    $description = null,
    $meta_data = null
){
    DB::table('user_activity_logs')->insert([
        'organization_id' => $organization_id,
        'user_id'         => $user_id,
        'entity_type'     => $entity_type,
        'entity_id'       => $entity_id,
        'action'          => $action,
        'description'     => $description,
        'meta_data'       => $meta_data ? json_encode($meta_data, JSON_UNESCAPED_UNICODE) : null,
        'ip_address'      => request()->ip(),
        'device'          => request()->header('User-Agent'),
        'browser'         => null,
        'os'              => null,
        'created_at'      => now(),
    ]);
}


/**
 * 3️⃣ LEAD ACTIVITY LOG
 * Table: lead_activity_logs
 */
function logLeadActivity(
    $organization_id,
    $lead_id,
    $user_id = null,
    $action = null,
    $description = null,
    $meta_data = null
){
    DB::table('lead_activity_logs')->insert([
        'organization_id' => $organization_id,
        'lead_id'         => $lead_id,
        'user_id'         => $user_id,
        'action'          => $action,
        'description'     => $description,
        'meta_data'       => $meta_data ? json_encode($meta_data, JSON_UNESCAPED_UNICODE) : null,
        'created_at'      => now(),
    ]);
}


/**
 * 4️⃣ LEAD ASSIGNMENT LOG
 * Table: lead_assignment_logs
 * (AUTO + MANUAL assignment tracking)
 */
function logLeadAssignment(
    $organization_id,
    $lead_id,
    $assigned_to,
    $lead_source_id = null,
    $previous_assigned_to = null,
    $rule_type = null,
    $rule_id = null,
    $is_reassign = false,
    $notes = null
){
    DB::table('lead_assignment_logs')->insert([
        'organization_id'        => $organization_id,
        'lead_id'                => $lead_id,
         'assigned_to'            => $assigned_to,
       'lead_source_id'         => $lead_source_id,
        'previous_assigned_to'   => $previous_assigned_to,
        'rule_type'              => $rule_type,
        'rule_id'                => $rule_id,
        'is_reassign'            => $is_reassign ? 1 : 0,
        'notes'                  => $notes,
        'created_at'             => now(),
        'updated_at'             => now(),
    ]);
}


/**
 * 5️⃣ SMS LOG
 */
function logSmsActivity(
    $organization_id,
    $lead_id = null,
    $mobile = null,
    $message = null,
    $provider = null,
    $provider_message_id = null,
    $status = 'pending',
    $response_json = null,
    $sent_by = null,
    $full_url = null,
    $communication_log_id = null   // ✅ NEW
){
    DB::table('sms_logs')->insert([
        'communication_log_id' => $communication_log_id,  // ✅ NEW
        'organization_id'      => $organization_id,
        'lead_id'              => $lead_id,
        'mobile'               => $mobile,
        'message'              => $message,
        'provider'             => $provider,
        'provider_message_id'  => $provider_message_id,
        'status'               => $status,
        'response_json'        => $response_json ? json_encode($response_json) : null,
        'sent_by'              => $sent_by,
        'full_url'             => $full_url,

        'attempts'             => 0,
        'sent_at'              => null,
        'last_attempt_at'      => null,

        'created_at'           => now(),
        'updated_at'           => now(),
    ]);
}




/**
 * 6️⃣ EMAIL LOG
 */
function logEmailActivity(
    $organization_id,
    $user_id,
    $template_name = null,
    $to_email = null,
    $subject = null,
    $body = null,
    $status = 'pending',
    $response_message = null,
    $provider = null,
    $sent_by = null,
    $communication_log_id = null   // ✅ NEW
){
    DB::table('email_logs')->insert([
        'communication_log_id' => $communication_log_id,  // ✅ NEW
        'organization_id'      => $organization_id,
        'user_id'              => $user_id,
        'template_name'        => $template_name,
        'to_email'             => $to_email,
        'subject'              => $subject,
        'body'                 => $body,
        'status'               => $status,

        'attempts'             => 0,
        'sent_at'              => null,
        'last_attempt_at'      => null,

        'response_message'     => $response_message,
        'provider'             => $provider,
        'sent_by'              => $sent_by,
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);
}
/**
 * 6️⃣ whastapp LOG
 */
function logWhatsappActivity(
    $organization_id,
    $lead_id = null,
    $mobile = null,
    $message = null,
    $template_id = null,
    $media_url = null,
    $provider = null,
    $provider_message_id = null,
    $status = 'pending',
    $response_json = null,
    $sent_by = null,
    $communication_log_id = null   // ✅ NEW
){
    DB::table('whatsapp_logs')->insert([
        'communication_log_id' => $communication_log_id, // ✅ NEW
        'organization_id'      => $organization_id,
        'lead_id'              => $lead_id,
        'mobile'               => $mobile,
        'template_id'          => $template_id,
        'message'              => $message,
        'media_url'            => $media_url,

        'provider'             => $provider,
        'provider_message_id'  => $provider_message_id,
        'status'               => $status,

        'attempts'             => 0,
        'sent_at'              => null,
        'last_attempt_at'      => null,

        'response_json'        => $response_json ? json_encode($response_json) : null,
        'sent_by'              => $sent_by,
        'created_at'           => now(),
        'updated_at'           => now(),
    ]);
}



//logLeadActivity($orgId, $leadId, $userId, 'status_changed', 'Lead moved to Interested');
  function render($slug)
{
    $page = LandingPage::where('slug', $slug)->firstOrFail();

    $sections = LandingPageSection::where('page_id', $page->id)
                ->orderBy('sort_order')
                ->get()
                ->map(function($sec) {
                    return [
                        'key'    => $sec->section_key,
                        'content'=> json_decode($sec->content_json, true)
                    ];
                })
                ->toArray();

    // Make available globally (for helper)
    global $sectionsMap;
    $sectionsMap = $sections;

    return view('landing.render', compact('page', 'sections'));
}

function renderSection(string $key): string
{
    global $sectionsMap;

    if (!isset($sectionsMap) || !is_array($sectionsMap)) {
        return "<!-- no sectionsMap -->";
    }

    foreach ($sectionsMap as $section) {

        if (!isset($section['key'])) continue;

        if ($section['key'] === $key) {

            $content = $section['content'] ?? [];

            try {
                return view("landing.sections.$key", ['content' => $content])->render();
            } catch (\Throwable $e) {
                return "<!-- section render error: {$e->getMessage()} -->";
            }
        }
    }

    return "<!-- section '$key' not found -->";
} 

function canViewField($field)
{
    $user = Sentinel::check();
    if (!$user) return false;

    $role  = $user->roles()->first()->slug ?? null;
    $orgId = registeruser($user->id)->organization ?? null;

    return LeadSetting::where([
        'organization_id' => $orgId,
        'role_slug'       => $role,
        'field'           => $field
    ])->value('can_view') == 1;
}

function maskMobile($mobile)
{
    if (!$mobile) return '';
    return substr($mobile, 0, 2) . '******' . substr($mobile, -2);
}

function maskEmail($email)
{
    if (!$email) return '';
    [$n,$d] = explode('@', $email);
    return substr($n,0,2).'****@'.$d;
}




 
function renderCommunicationTemplate(string $text, $lead, $orgId = null)
{
    if (!$text) return $text;

    // ---------- BASIC LEAD ----------
    $leadId    = $lead->id ?? '';
    $leadName  = $lead->name ?? '';
    $leadMobile= $lead->mobile ?? '';
    $leadEmail = $lead->email ?? '';
    $leadCity  = $lead->city ?? '';
    $leadState = $lead->state ?? '';

    $leadNextFollowup = !empty($lead->next_followup_at)
        ? \Carbon\Carbon::parse($lead->next_followup_at)->format('d M Y, h:i A')
        : '';

    $leadRegDate = !empty($lead->lead_registration_date)
        ? \Carbon\Carbon::parse($lead->lead_registration_date)->format('d M Y, h:i A')
        : '';

    // ---------- LEAD SOURCE ----------
    $leadSourceName = '';
    if (!empty($lead->lead_source_id)) {
        $leadSourceName = DB::table('lead_sources')
            ->where('id', $lead->lead_source_id)
            ->value('name') ?? '';
    }

    // ---------- PROGRAMME ----------
    $programmeName = '';
    if (!empty($lead->programme_id)) {
        $programmeName = DB::table('programs')
            ->where('id', $lead->programme_id)
            ->value('name') ?? '';
    }

    // ---------- INSTITUTE ----------
    $instituteName = '';
    if (!empty($lead->institute_id)) {
        $instituteName = DB::table('institutes')
            ->where('id', $lead->institute_id)
            ->value('name') ?? '';
    }

    // ---------- ASSIGNED USER ----------
    $assignedUserName = '';
    if (!empty($lead->lead_assign_to)) {

        // NOTE: your users are in registers table
        $assignedUserName = DB::table('registers')
            ->where('id', $lead->lead_assign_to)
            ->selectRaw("TRIM(CONCAT(IFNULL(first_name,''),' ',IFNULL(last_name,''))) as full_name")
            ->value('full_name') ?? '';
    }

    // ---------- VARIABLE MAP ----------
    $map = [
        // lead
        '{lead_id}'                => $leadId,
        '{lead_name}'              => $leadName,
        '{lead_mobile}'            => $leadMobile,
        '{lead_email}'             => $leadEmail,
        '{lead_city}'              => $leadCity,
        '{lead_state}'             => $leadState,
        '{lead_next_followup}'     => $leadNextFollowup,
        '{lead_registration_date}' => $leadRegDate,
        '{lead_assign_to}'         => $lead->lead_assign_to ?? '',

        // customer (same as lead)
        '{customer_name}'          => $leadName,
        '{customer_email}'         => $leadEmail,
        '{customer_mobile}'        => $leadMobile,

        // source / programme / institute
        '{lead_source}'            => $leadSourceName,
        '{programme_name}'         => $programmeName,
        '{institute_name}'         => $instituteName,

        // assigned
        '{assigned_user}'          => $assignedUserName,
        '{assigned_user_name}'     => $assignedUserName,
    ];

    return strtr($text, $map);
}
