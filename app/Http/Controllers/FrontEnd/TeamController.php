<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\Team;
use App\Register;
use App\Organization;
use Illuminate\Http\Request;
use Sentinel;
use Illuminate\Support\Facades\Crypt;

class TeamController extends Controller
{
    // ---------------------------------------------------------
    // INDEX — ROLE BASED TEAM LISTING
    // ---------------------------------------------------------
public function index()
{
    // ------------------------------------------------
    // Logged-in user context
    // ------------------------------------------------
    $user = Sentinel::check();
    $uid  = $user->id;
    $role = $user->roles()->first()->slug ?? null;

    $reg   = registeruser($uid);
    $orgId = $reg->organization ?? null;

    // ------------------------------------------------
    // Base Team Query
    // ------------------------------------------------
    $query = Team::with('organization')->orderBy('id', 'DESC');

    // ------------------------------------------------
    // ROOT → All organizations, all teams, all users
    // ------------------------------------------------
    if ($role === 'root') {

        $organizations = Organization::orderBy('name')->get();
        $teams = $query->get();

        $registerUsers = Register::select('id', 'first_name', 'last_name', 'avatar')
            ->orderBy('first_name')
            ->get();
    }

    // ------------------------------------------------
    // ADMIN / SADMIN → Same organization only
    // ------------------------------------------------
    elseif (in_array($role, ['admin', 'sadmin'])) {

        $organizations = Organization::where('id', $orgId)->get();

        $teams = $query
            ->where('organization_id', $orgId)
            ->get();

        $registerUsers = Register::where('organization', $orgId)
            ->select('id', 'first_name', 'last_name', 'avatar')
            ->orderBy('first_name')
            ->get();
    }

    // ------------------------------------------------
    // MANAGER / TEAM / OPERATOR → Assigned teams only
    // ------------------------------------------------
    else {

        $organizations = Organization::where('id', $orgId)->get();

        $teams = $query
            ->where('organization_id', $orgId)
            ->where(function ($q) use ($uid) {
                $q->where('head', $uid)
                  ->orWhereRaw("FIND_IN_SET(?, team_member)", [$uid]);
            })
            ->get();

        $registerUsers = Register::where('organization', $orgId)
            ->select('id', 'first_name', 'last_name', 'avatar')
            ->orderBy('first_name')
            ->get();
    }

    // ------------------------------------------------
    // USERS (same name) + USERS AVATAR (new)
    // ------------------------------------------------
    $users = $registerUsers->mapWithKeys(function ($u) {
        return [
            $u->id => trim($u->first_name . ' ' . $u->last_name)
        ];
    })->toArray();

    $usersAvatar = $registerUsers->mapWithKeys(function ($u) {
        return [
            $u->id => $u->avatar
        ];
    })->toArray();

    // ------------------------------------------------
    // View
    // ------------------------------------------------
    return view('FrontEnd.teams.index', compact(
        'teams',
        'organizations',
        'users',
        'usersAvatar',
        'role',
        'orgId'
    ));
}


    // ---------------------------------------------------------
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required',
            'team_name'       => 'required',
            'head'            => 'required',
            'status'          => 'required',
            'members'         => 'required|array'
        ]);

        $createdBy = Sentinel::getUser()->id;

        $team = Team::create([
            'organization_id' => $request->organization_id,
            'name'            => $request->team_name,
            'team_member'     => implode(',', $request->members),
            'status'          => $request->status,
            'head'            => $request->head,
            'created_by'      => $createdBy,
        ]);

        // LOGS
        logOrganizationActivity(
            $team->organization_id,
            $createdBy,
            'team',
            $team->id,
            'create_team',
            "Team '{$team->name}' created",
            $validated
        );

        logUserActivity(
            $team->organization_id,
            $createdBy,
            'team',
            $team->id,
            'create_team',
            "User created team '{$team->name}'",
            $validated
        );

        return response()->json(['status'=>'success']);
    }

    // ---------------------------------------------------------
    public function show($id)
    {
      try {
        $id = Crypt::decrypt($id);
    } catch (\Exception $e) {
        abort(404);
    }

    $team = Team::with(['organization','headUser'])->findOrFail($id);

        $members = Register::whereIn('id', explode(',',$team->team_member))->get();

        $member_names = $members->map(fn($m)=>
            '<span class="badge bg-primary-subtle text-primary me-1">'.
            $m->first_name.' '.$m->last_name.'</span>'
        )->implode('');

        return response()->json([
            'id'            => $team->id,
            'name'          => $team->name,
            'member_id'     => $team->team_member,
            'organization'  => $team->organization->name ?? '-',
            'head_name'     => $team->headUser->first_name.' '.$team->headUser->last_name,
            'member_names'  => $member_names,
            'created_at'    => $team->created_at->format('d M, Y | h:i A'),
            'status'        => $team->status,
        ]);
    }

    // ---------------------------------------------------------
    public function edit($id)
    {
        $team = Team::findOrFail($id);

        return response()->json([
            'id'              => $team->id,
            'name'            => $team->name,
            'organization_id' => $team->organization_id,
            'head'            => $team->head,
            'status'          => $team->status,
            'members'         => explode(',',$team->team_member)
        ]);
    }

    // ---------------------------------------------------------
    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $oldData = $team->toArray();

        $team->update([
            'name'            => $request->team_name,
            'organization_id' => $request->organization_id,
            'head'            => $request->head,
            'status'          => $request->status,
            'team_member'     => implode(",", $request->members),
        ]);

        $updatedBy = Sentinel::getUser()->id;

        // LOGS
        logOrganizationActivity(
            $team->organization_id,
            $updatedBy,
            'team',
            $team->id,
            'update_team',
            "Team '{$team->name}' updated",
            ['old'=>$oldData,'new'=>$request->all()]
        );

        logUserActivity(
            $team->organization_id,
            $updatedBy,
            'team',
            $team->id,
            'update_team',
            "User updated team '{$team->name}'",
            ['old'=>$oldData,'new'=>$request->all()]
        );

        return response()->json(['status'=>'success']);
    }

    // ---------------------------------------------------------
    public function getUsers($orgId)
    {
        return response()->json(
            Register::where('organization', $orgId)->get()
        );
    }
}
