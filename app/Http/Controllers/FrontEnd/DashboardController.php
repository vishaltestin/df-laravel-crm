<?php
namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Sentinel;
use App\User;
use App\Register;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // ✅ THIS LINE

class DashboardController extends Controller
{
    /**
     * Dashboard main view
     */
  public function index()
{
    // =============================
    // AUTH + ORG + ROLE
    // =============================
    $user  = Sentinel::check();
    $reg   = registeruser($user->id);
    $orgId = $reg->organization;
    $role  = optional($user->roles()->first())->slug;

    $teamUserIds = [$user->id];

    // =============================
    // BASE LEADS QUERY
    // =============================
    $baseLeads = DB::table('leads')
        ->where('leads.organization_id', $orgId);

    // =============================
    // ROLE FILTER
    // =============================
    if ($role === 'manager') {

        $team = DB::table('teams')
            ->where('head', $user->id)
            ->where('status', 1)
            ->first();

        if ($team && $team->team_member) {
            $members = array_filter(explode(',', $team->team_member));
            $teamUserIds = array_merge($teamUserIds, $members);
        }

        $baseLeads->whereIn('leads.lead_assign_to', $teamUserIds);

    } elseif (!in_array($role, ['sadmin','admin'])) {

        $baseLeads->where('leads.lead_assign_to', $user->id);
    }

    // =============================
    // VERIFIED / UNVERIFIED LEADS
    // =============================
    $verified = (clone $baseLeads)
        ->whereExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('lead_verifications')
              ->whereColumn('lead_verifications.lead_id', 'leads.id')
              ->where(function ($v) {
                  $v->where('sms_verified', 1)
                    ->orWhere('email_verified', 1);
              });
        })->count();

    $unverified = (clone $baseLeads)
        ->where(function ($q) {
            $q->whereNotExists(function ($v) {
                $v->select(DB::raw(1))
                  ->from('lead_verifications')
                  ->whereColumn('lead_verifications.lead_id', 'leads.id');
            })
            ->orWhereExists(function ($v) {
                $v->select(DB::raw(1))
                  ->from('lead_verifications')
                  ->whereColumn('lead_verifications.lead_id', 'leads.id')
                  ->where('sms_verified', 0)
                  ->where('email_verified', 0);
            });
        })->count();

    $total = $verified + $unverified;

    $verifiedPercent   = $total ? round(($verified / $total) * 100) : 0;
    $unverifiedPercent = 100 - $verifiedPercent;

    // =============================
    // CONVERTED LEADS (PAID APPLICATION EXISTS)
    // =============================
    $convertedLeads = (clone $baseLeads)
        ->whereExists(function ($q) {
            $q->select(DB::raw(1))
              ->from('application_form_responses')
              ->whereColumn('application_form_responses.lead_id', 'leads.id')
              ->where('application_form_responses.payment_status', 1);
        })->count();

    $leadConversion = $total
        ? round(($convertedLeads / $total) * 100, 2)
        : 0;

    // =============================
    // APPLICATION BASE QUERY
    // =============================
    $appBase = DB::table('application_form_responses')
        ->where('organization_id', $orgId);

    if ($role === 'manager') {
        $appBase->whereIn('assigned_to', $teamUserIds);
    } elseif (!in_array($role, ['sadmin','admin'])) {
        $appBase->where('assigned_to', $user->id);
    }

    // =============================
    // APPLICATION COUNTS
    // =============================
    $totalApplications  = (clone $appBase)->count();
    $paidApplications   = (clone $appBase)->where('payment_status', 1)->count();
    $unpaidApplications = $totalApplications - $paidApplications;

    // =============================
    // DEAL TYPE (DONUT)
    // =============================
    $dealPaid   = $paidApplications;
    $dealUnpaid = $unpaidApplications;
    $dealFree   = (clone $appBase)->where('payment_method', 'free')->count();

    // =============================
    // PAID QUERY (REVENUE)
    // =============================
    $paidQuery = (clone $appBase)->where('payment_status', 1);

  $paidByMethod = (clone $paidQuery)
    ->selectRaw('payment_method, COUNT(*) as total')
    ->groupBy('payment_method')
    ->get()
    ->keyBy('payment_method');

$paidOnlineCount  = $paidByMethod['online']->total  ?? 0;
$paidOfflineCount = $paidByMethod['offline']->total ?? 0;
$paidCashCount    = $paidByMethod['cash']->total    ?? 0;
$paidDDCount      = $paidByMethod['dd']->total      ?? 0;
$paidFreeCount    = $paidByMethod['free']->total    ?? 0;

$totalPaidCount = $paidOnlineCount
                + $paidOfflineCount
                + $paidCashCount
                + $paidDDCount
                + $paidFreeCount;


    // =============================
    // FUNNEL
    // =============================
    $funnelLeads        = $total;
    $funnelApplications = $totalApplications;
    $funnelPaid         = $paidApplications;

    $funnelEnrolled = (clone $appBase)
        ->where('payment_status', 1)
        ->where('status', 'approved')
        ->count();

    // =============================
    // SOURCE PERFORMANCE
    // =============================
    $leadSources = DB::table('leads')
        ->leftJoin('lead_sources','lead_sources.id','=','leads.lead_source_id')
        ->selectRaw('lead_sources.name as source, COUNT(leads.id) as total')
        ->where('leads.organization_id', $orgId)
        ->groupBy('lead_sources.name')
        ->get();

    $sourceLabels = $leadSources->pluck('source');
    $sourceData   = $leadSources->pluck('total');

    // =============================
    // TEAM PERFORMANCE
    // =============================
    $teamStats = DB::table('leads')
        ->selectRaw('lead_assign_to, COUNT(*) as total')
        ->where('organization_id', $orgId)
        ->groupBy('lead_assign_to')
        ->get();

    $teamPaidStats = DB::table('application_form_responses')
        ->selectRaw('assigned_to, COUNT(*) as paid')
        ->where('organization_id', $orgId)
        ->where('payment_status', 1)
        ->groupBy('assigned_to')
        ->get()
        ->keyBy('assigned_to');

    $teamLabels = [];
    $teamLeads  = [];
    $teamPaid   = [];

    foreach ($teamStats as $row) {
        $teamLabels[] = 'User '.$row->lead_assign_to;
        $teamLeads[]  = $row->total;
        $teamPaid[]   = $teamPaidStats[$row->lead_assign_to]->paid ?? 0;
    }

    // =============================
    // REVENUE SNAPSHOT
    // =============================
    $todayRevenue = (clone $paidQuery)
        ->whereDate('created_at', \Carbon\Carbon::today())
        ->sum('payment_amount');

    $monthRevenue = (clone $paidQuery)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('payment_amount');

    // =============================
    // FOLLOW UPS
    // =============================
    $pendingFollowups = DB::table('leads')
        ->where('organization_id', $orgId)
        ->whereNotNull('next_followup_at')
        ->where('next_followup_at','<=', now())
        ->count();

    // =============================
    // RECENT LEADS
    // =============================
    $recentLeads = DB::table('leads')
        ->where('organization_id', $orgId)
        ->latest('id')
        ->limit(10)
        ->get();
// =============================
// QUERY / TICKET STATS (FROM lead_queries)
// =============================
$queryBase = DB::table('lead_queries')
    ->join('leads','leads.id','=','lead_queries.lead_id')
    ->where('leads.organization_id', $orgId);

// Role based filter
if ($role === 'manager') {
    $queryBase->whereIn('leads.lead_assign_to', $teamUserIds);
} elseif (!in_array($role, ['sadmin','admin'])) {
    $queryBase->where('leads.lead_assign_to', $user->id);
}

$totalQueries = (clone $queryBase)->count();
$openQueries = (clone $queryBase)->where('lead_queries.status','open')->count();
$inProgressQueries = (clone $queryBase)->where('lead_queries.status','in_progress')->count();
$closedQueries = (clone $queryBase)->where('lead_queries.status','closed')->count();

    // =============================
    // RETURN VIEW (BLADE SAFE)
    // =============================
  return view('FrontEnd.dashboard', compact(
    'total','verified','unverified',
    'verifiedPercent','unverifiedPercent','leadConversion',

    'totalApplications','paidApplications','unpaidApplications',

    // Deal / Paid counts
    'dealPaid','dealUnpaid','dealFree',
    'paidOnlineCount','paidOfflineCount','paidCashCount',
    'paidDDCount','paidFreeCount','totalPaidCount',

    // Funnel
    'funnelLeads','funnelApplications','funnelPaid','funnelEnrolled',

    // Revenue (amount based – correct)
    'todayRevenue','monthRevenue',

    // Others
    'pendingFollowups','leadSources',
    'recentLeads',
    'sourceLabels','sourceData',
    'teamLabels','teamLeads','teamPaid',

    // Tickets
    'totalQueries','openQueries','inProgressQueries','closedQueries'
));


}










   /**
     * Total Leads Card
     */
    public function totalLeads(Request $request)
    {
        // total leads
        // verified / unverified breakup
    }

    /**
     * Paid Applications Card
     */
    public function paidApplications(Request $request)
    {
        // total paid amount
        // online / offline / cash / dd / free breakup
    }

    /**
     * Daily Average Income Card
     */
    public function dailyAverageIncome(Request $request)
    {
        // total income / total days
    }

    /**
     * Lead Conversion (Enrollments %)
     */
    public function leadConversion(Request $request)
    {
        // converted leads / total leads * 100
    }

    /**
     * Unpaid Applications Card
     */
    public function unpaidApplications(Request $request)
    {
        // unpaid amount
        // processed / unapproved breakup
    }

    /**
     * Queries / Deals Card
     */
public function queriesDeals(Request $request)
{
    $user  = Sentinel::check();
    $orgId = registeruser($user->id)->organization;
    $role  = optional($user->roles()->first())->slug;

    $queryBase = DB::table('lead_queries')
        ->join('leads','leads.id','=','lead_queries.lead_id')
        ->where('leads.organization_id', $orgId);

    // Role filter
    if (!in_array($role,['admin','sadmin'])) {
        $queryBase->where('leads.lead_assign_to', $user->id);
    }

    $totalTickets = (clone $queryBase)->count();

    $openTickets = (clone $queryBase)
        ->where('lead_queries.status','open')
        ->count();

    $inProgressTickets = (clone $queryBase)
        ->where('lead_queries.status','in_progress')
        ->count();

    $closedTickets = (clone $queryBase)
        ->where('lead_queries.status','closed')
        ->count();

    return response()->json([
        'total'       => $totalTickets,
        'open'        => $openTickets,
        'in_progress' => $inProgressTickets,
        'closed'      => $closedTickets,
    ]);
}


    /**
     * Dashboard filters (date / range / source)
     */
    public function filters(Request $request)
    {
        // date range
        // today / month / custom
    }
	
	
	
	public function graphs()
{
    $user = \Sentinel::check();
    $orgId = registeruser($user->id)->organization;

    // ===============================
    // 1. SALES FORECAST (MONTHLY)
    // ===============================
    $sales = \DB::table('application_form_responses')
        ->selectRaw('MONTH(created_at) as month, SUM(payment_amount) as total')
        ->where('organization_id', $orgId)
        ->where('payment_status', 1)
        ->groupByRaw('MONTH(created_at)')
        ->orderByRaw('MONTH(created_at)')
        ->get();

    $salesData = array_fill(1, 12, 0);
    foreach ($sales as $row) {
        $salesData[$row->month] = (float) $row->total;
    }

    // ===============================
    // 2. DEAL TYPE (PAID / UNPAID)
    // ===============================
    $dealType = [
        'Paid'   => \DB::table('application_form_responses')
                        ->where('organization_id',$orgId)
                        ->where('payment_status',1)
                        ->count(),
        'Unpaid' => \DB::table('application_form_responses')
                        ->where('organization_id',$orgId)
                        ->where('payment_status',0)
                        ->count(),
    ];

    // ===============================
    // 3. BALANCE OVERVIEW (MONTHLY)
    // ===============================
    $revenue = \DB::table('application_form_responses')
        ->selectRaw('MONTH(created_at) as month, SUM(payment_amount) as total')
        ->where('organization_id',$orgId)
        ->where('payment_status',1)
        ->groupByRaw('MONTH(created_at)')
        ->get();

    $revenueData = array_fill(1, 12, 0);
    foreach ($revenue as $r) {
        $revenueData[$r->month] = (float) $r->total;
    }

    // dummy expense logic (40%)
    $expenseData = array_map(fn($v) => round($v * 0.4), $revenueData);

    return response()->json([
        'sales_forecast' => array_values($salesData),
        'deal_type'      => $dealType,
        'revenue'        => array_values($revenueData),
        'expenses'       => array_values($expenseData),
    ]);
}

	
	
	
	
	
	
	
	
	
}