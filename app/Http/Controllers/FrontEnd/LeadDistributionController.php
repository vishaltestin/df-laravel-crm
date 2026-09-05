<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\LeadDistributionRule;
use App\LeadDistributionRuleItem;
use App\LeadSource;
use App\Team;
use App\Designation;
use App\EmailTemplate;
use App\Register;
use App\Services\LeadNotificationService;
use Sentinel;

class LeadDistributionController extends Controller
{
    protected function orgId()
    {
        return Auth::user()->organization_id ?? 1;
    }

    // ---------------------------------------------------------
    public function index()
    {
        $user = \Sentinel::check();
        $userId = $user->id;

        $reg = registeruser($userId);
        $organizationId = $reg->organization;

        $role = $user->roles()->first()->slug ?? null;

        if ($role === 'root') {
            $users = Register::orderBy('first_name')->get(['id', 'first_name as name']);
        } else {
            $users = Register::where('organization', $organizationId)
                ->orderBy('first_name')
                ->get(['id','first_name as name']);
        }

        $rules = LeadDistributionRule::with('items')
            ->where('organization_id', $organizationId)
            ->get();

        $sources = LeadSource::where('organization_id',$organizationId)->get();
        $teams = Team::where('organization_id',$organizationId)->get();
        $designations = Designation::orderBy('name')->get();

        return view('FrontEnd.lead_distribution.index', compact(
            'rules','sources','teams','users','designations','role'
        ));
    }



    // ---------------------------------------------------------
    // STORE RULE
    // ---------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'rule_name'         => 'required|string|max:255',
            'distribution_type' => 'required|in:random,source',
        ]);

        $user   = \Sentinel::check();
        $role   = $user->roles()->first()->slug ?? null;
        $uid    = $user->id;
        $reg    = registeruser($uid);
        $orgId  = $reg->organization;

        if (!($role === 'root' || in_array($role, ['admin','sadmin']))) {
            return response()->json([
                'status'=>false,
                'message'=>'You have view only access!'
            ],403);
        }

        $rule = LeadDistributionRule::create([
            'organization_id'   => $orgId,
            'rule_name'         => $request->rule_name,
            'distribution_type' => $request->distribution_type,
            'is_active'         => $request->has('is_active') ? 1 : 0,
        ]);

        // ----------------------------------
        // INSERT RULE ITEMS
        // ----------------------------------
        if ($request->distribution_type === 'random' && is_array($request->selected_users)) {
            foreach ($request->selected_users as $uid2) {
                if (!$uid2) continue;

                LeadDistributionRuleItem::create([
                    'rule_id'=>$rule->id,
                    'assign_type'=>'user',
                    'user_id'=>$uid2
                ]);
            }
        }

        if ($request->distribution_type === 'source' && is_array($request->items)) {
            foreach ($request->items as $item) {

                if (!$item['source_id'] || !$item['assign_type'] || !$item['assign_id'])
                    continue;

                $data = [
                    'rule_id'     => $rule->id,
                    'source_id'   => $item['source_id'],
                    'assign_type' => $item['assign_type'],
                    'percentage'  => $item['percentage'] ?? null,
                ];

                if ($item['assign_type'] === 'team')
                    $data['team_id'] = $item['assign_id'];

                elseif ($item['assign_type'] === 'user')
                    $data['user_id'] = $item['assign_id'];

                elseif ($item['assign_type'] === 'designation')
                    $data['designation_id'] = $item['assign_id'];

                LeadDistributionRuleItem::create($data);
            }
        }

        // ---------------------------------------------------
        // 🔥 LOGS: ORG + USER
        // ---------------------------------------------------
        logOrganizationActivity(
            $orgId,
            $uid,
            'lead_distribution_rule',
            $rule->id,
            'create_rule',
            "Lead distribution rule created: {$request->rule_name}",
            $rule->toArray()
        );

        logUserActivity(
            $orgId,
            $uid,
            'lead_distribution_rule',
            $rule->id,
            'create_rule',
            "User created lead distribution rule: {$request->rule_name}",
            $rule->toArray()
        );

        return response()->json([
            'status'=>true,
            'message'=>'Rule created successfully.'
        ]);
    }



    // ---------------------------------------------------------
    // EDIT
    // ---------------------------------------------------------
    public function edit($id)
    {
        $user = \Sentinel::check();
        $uid  = $user->id;
        $role = $user->roles()->first()->slug ?? null;

        $reg  = registeruser($uid);
        $orgId = $reg->organization;

        $rule = LeadDistributionRule::with('items')
            ->where('organization_id',$orgId)
            ->findOrFail($id);

        if (!($role === 'root' || in_array($role,['admin','sadmin']))) {
            return response()->json(['status'=>false,'message'=>'View only access'],403);
        }

        return response()->json($rule);
    }



    // ---------------------------------------------------------
    // UPDATE RULE
    // ---------------------------------------------------------
    public function update(Request $request, $id)
    {
        $user = \Sentinel::check();
        $uid  = $user->id;
        $role = $user->roles()->first()->slug ?? null;

        $reg  = registeruser($uid);
        $orgId = $reg->organization;

        $rule = LeadDistributionRule::where('organization_id',$orgId)->findOrFail($id);
        $oldData = $rule->toArray();

        if (!($role === 'root' || in_array($role,['admin','sadmin']))) {
            return response()->json(['status'=>false,'message'=>'View only access'],403);
        }

        $rule->update([
            'rule_name'         => $request->rule_name,
            'distribution_type' => $request->distribution_type,
            'is_active'         => $request->has('is_active') ? 1 : 0,
        ]);

        LeadDistributionRuleItem::where('rule_id',$rule->id)->delete();

        // INSERT NEW ITEMS AGAIN
        if ($request->distribution_type === 'random' && is_array($request->selected_users)) {
            foreach ($request->selected_users as $uid2) {
                if (!$uid2) continue;

                LeadDistributionRuleItem::create([
                    'rule_id'=>$rule->id,
                    'assign_type'=>'user',
                    'user_id'=>$uid2
                ]);
            }
        }

        if ($request->distribution_type === 'source' && is_array($request->items)) {
            foreach($request->items as $item){
                if (!$item['source_id'] || !$item['assign_type'] || !$item['assign_id'])
                    continue;

                $data=[
                    'rule_id'=>$rule->id,
                    'source_id'=>$item['source_id'],
                    'assign_type'=>$item['assign_type'],
                    'percentage'=>$item['percentage']??null
                ];

                if($item['assign_type']=='team') $data['team_id']=$item['assign_id'];
                if($item['assign_type']=='user') $data['user_id']=$item['assign_id'];
                if($item['assign_type']=='designation') $data['designation_id']=$item['assign_id'];

                LeadDistributionRuleItem::create($data);
            }
        }

        // ---------------------------------------------------
        // 🔥 LOGGING
        // ---------------------------------------------------
        logOrganizationActivity(
            $orgId,
            $uid,
            'lead_distribution_rule',
            $rule->id,
            'update_rule',
            "Lead distribution rule updated",
            ['old'=>$oldData, 'new'=>$request->all()]
        );

        logUserActivity(
            $orgId,
            $uid,
            'lead_distribution_rule',
            $rule->id,
            'update_rule',
            "User updated lead distribution rule",
            ['old'=>$oldData, 'new'=>$request->all()]
        );

        return response()->json([
            'status'=>true,
            'message'=>'Rule updated!'
        ]);
    }



    // ---------------------------------------------------------
    // DELETE RULE
    // ---------------------------------------------------------
    public function destroy($id)
    {
        $user = \Sentinel::check();
        $uid  = $user->id;
        $role = $user->roles()->first()->slug ?? null;

        $reg  = registeruser($uid);
        $orgId = $reg->organization;

        $rule = LeadDistributionRule::where('organization_id',$orgId)->findOrFail($id);
        $oldData = $rule->toArray();

        if (!($role === 'root' || in_array($role,['admin','sadmin']))) {
            return response()->json(['status'=>false,'message'=>'View only access'],403);
        }

        $rule->delete();

        // ---------------------------------------------------
        // 🔥 LOGGING
        // ---------------------------------------------------
        logOrganizationActivity(
            $orgId,
            $uid,
            'lead_distribution_rule',
            $id,
            'delete_rule',
            "Lead distribution rule deleted",
            $oldData
        );

        logUserActivity(
            $orgId,
            $uid,
            'lead_distribution_rule',
            $id,
            'delete_rule',
            "User deleted lead distribution rule",
            $oldData
        );

        return response()->json([
            'status'=>true,
            'message'=>'Rule deleted successfully!'
        ]);
    }

}
