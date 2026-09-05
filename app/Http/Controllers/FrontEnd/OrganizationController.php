<?php

namespace App\Http\Controllers\FrontEnd;

use App\Organization;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Countries;
use App\Designation;
use App\Team;
use App\User;
use App\Register;
use App\Role;
use Sentinel;
use Activation;
use App\Client;
use Session;
use Auth;
use Route;
use Hash;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class OrganizationController extends Controller
{
    public function index()
    {
      $user   = \Sentinel::check();
          $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;
          $userId = $reg->id;

        $role  = $user->roles()->first()->slug ?? null;

        // ---------------------------
        // ROLE BASED ACCESS
        // ---------------------------
        if ($role === 'root') {
            // root → all organizations
            $organizations = Organization::latest()->get();
        }
        elseif ($role === 'sadmin') {
            // sadmin → all organizations (but delete denied)
            $organizations = Organization::latest()->get();
        }
        elseif ($role === 'admin') {
				 // admin → only own organization
                 $organizations = Organization::where('id', $orgId)
				->orWhere('created_by', $userId)
 				->get();
        }
        else {
            // manager / operator → no access
            return abort(403, "Unauthorized Access");
        }

        $countries = Countries::all();

        return view('FrontEnd.organization.index', compact(
            'organizations',
            'countries',
            'orgId',
            'role'
        ));
    }

    public function create()
    {
        return view('FrontEnd.organization.create');
    }
	
	public function store(Request $request)
	{
		$user   = \Sentinel::check();
		$mainUserId = $user->id;

		$reg   = registeruser($mainUserId);
		$orgId = $reg->organization ?? null;
		$regId = $reg->id ?? null;

		$actorId = $regId ?: $mainUserId;
		$role  = $user->roles()->first()->slug ?? null;

		// =========================
		// ✅ VALIDATION (ROLE BASED)
		// =========================

		if ($role === 'admin') {
			$validated = $request->validate([
				'name' => 'required',
			 
				'city' => 'nullable',
				'state' => 'nullable',
				'country' => 'nullable',
				'zipcode' => 'nullable',
				'status' => 'required',
				'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
			]);
		} else {
			$validated = $request->validate([
				'name' => 'required',
				'first_name' => 'required',
				'last_name' => 'required',
				'email' => 'required|email|unique:organizations,email',
				'mobile' => 'required|unique:organizations,mobile',
				'city' => 'nullable',
				'state' => 'nullable',
				'country' => 'nullable',
				'zipcode' => 'nullable',
				'status' => 'required',
				'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
			]);
		}

		// =========================
		// FILE UPLOAD
		// =========================
		if ($request->hasFile('logo')) {
			$file = $request->file('logo');
			$filename = time().'_'.$file->getClientOriginalName();
			$destinationPath = public_path('uploads/organization');

			if (!file_exists($destinationPath)) {
				mkdir($destinationPath, 0755, true);
			}

			$file->move($destinationPath, $filename);
			$validated['logo'] = 'uploads/organization/' . $filename;
		}

		$validated['created_by'] = $actorId;

		// =========================
		// CREATE ORGANIZATION
		// =========================
		$organization = Organization::create($validated);

		logOrganizationActivity($organization->id,$actorId,'organization',$organization->id,'create_organization',"Organization '{$organization->name}' created",$validated);

		// =========================
		// DEFAULT DESIGNATION
		// =========================
		$designation = Designation::create([
			'name'=>'Administration',
			'organization_id'=>$organization->id,
			'status'=>1,
			'created_by'=>$actorId
		]);

		// =========================
		// DEFAULT TEAM
		// =========================
		$team = Team::create([
			'name'=>'Administration',
			'organization_id'=>$organization->id,
			'status'=>1,
			'created_by'=>$actorId,
			'head'=> $actorId,
			'team_member'=> $actorId
		]);

		// =========================
		// ✅ ADMIN CASE
		// =========================
		if ($role === 'admin') {

			$register = Register::where('user_id', $mainUserId)->first();

			if ($register) {
				$existing = $register->other_org 
					? explode(',', $register->other_org) 
					: [];

				if (!in_array($organization->id, $existing)) {
					$existing[] = $organization->id;
				}

				$register->update([
					'other_org' => implode(',', $existing)
				]);
				    // =========================
					// ✅ SESSION UPDATE (IMPORTANT 🔥)
					// =========================

					$currentOrg = $register->organization;

					$otherOrgs = $register->other_org 
						? explode(',', $register->other_org) 
						: [];

					$allOrgIds = array_unique(array_merge([$currentOrg], $otherOrgs));

					$orgData = \App\Organization::whereIn('id', $allOrgIds)
								->get(['id','name','logo'])
								->toArray();

					$orgIds = array_column($orgData, 'id');

					\Session::put('organization', $currentOrg); // current same रहेगा
					\Session::put('organizations', $orgIds);
					\Session::put('organization_data', $orgData);
			}

			return response()->json([
				'status'=>'success',
				'message'=>'Organization created successfully (Admin)',
				'organization'=>$organization
			]);
		}

		// =========================
		// ✅ NON-ADMIN CASE (USER CREATE)
		// =========================

		$password = $request->mobile;

		$newUser = Sentinel::registerAndActivate([
			'first_name'=>$request->first_name,
			'last_name'=>$request->last_name,
			'email'=>$request->email,
			'password'=>$password,
		]);

		$roleObj = Sentinel::findRoleBySlug('admin');
		$roleObj->users()->attach($newUser);

		Register::create([
			'status'=>1,
			'user_id'=>$newUser->id,
			'user_type'=>'3',
			'email'=>$request->email,
			'mobile'=>$request->mobile,
			'first_name'=>$request->first_name,
			'last_name'=>$request->last_name,
			'organization'=>$organization->id,
			'designation'=>$designation->id,
			'team'=>$team->id,
			'other_org'=>$organization->id,
		]);

		logUserActivity($organization->id,$actorId,'user',$newUser->id,'create_user',"Admin user '{$newUser->email}' created");

		return response()->json([
			'status'=>'success',
			'message'=>'Organization & User created successfully',
			'organization'=>$organization
		]);
	}
 public function switchOrganization($id)
{
    try {

        $orgId = decrypt($id);

        $user   = \Sentinel::check();
        $mainUserId = $user->id;

        $reg   = registeruser($mainUserId);
        $actorId = $reg->id ?? null;

        if (!$reg) {
            return redirect()->back()->with('error', 'User not found');
        }

        // ✅ current + other_org fetch
        $currentOrg = $reg->organization;
        $otherOrgs  = $reg->other_org ? explode(',', $reg->other_org) : [];

        // ✅ check allowed org
        $isAllowed = ($currentOrg == $orgId) || in_array($orgId, $otherOrgs);

        if (!$isAllowed) {
            return redirect()->back()->with('error', 'Unauthorized Organization');
        }

        // ✅ register table update (main org switch)
        $reg->update([
            'organization' => $orgId
        ]);

        // ✅ session update
        Session::put('organization', $orgId);

        // ✅ settings reload
        $setting = \App\GeneralSetting::where('organization_id', $orgId)->first();

        if ($setting) {
            session([
                'general_settings' => [
                    'app_name' => $setting->app_name,
                    'currency' => $setting->currency,
                    'timezone' => $setting->timezone,
                    'payment_credentials' => $setting->payment_credentials,
                ]
            ]);
        }

        return redirect()->back()->with('success', 'Organization switched');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Invalid Organization');
    }
}




public function show(string $id)
{
   try {
        $id = Crypt::decrypt($id);
    } catch (\Exception $e) {
        abort(404);
    }
	$organization = Organization::findOrFail($id);

    // ✅ All users with team + designation
    $users = Register::with('designationRel')
        ->select(
            'id',
            'first_name',
            'last_name',
            'designation',
            'organization',
            'team',
            'avatar',
            'status'
        )
        ->where('organization', $organization->id)
        ->orderBy('first_name')
        ->get();

    // ✅ All teams
    $teams = Team::where('organization_id', $organization->id)
        ->orderBy('name')
        ->get();

    // ✅ Team wise prepared data (VIEW READY)
    $teamData = $teams->map(function ($team) use ($users) {

        $teamUsers = $users->where('team', $team->id)->values();

        return [
            'id'           => $team->id,
            'name'         => $team->name,
            'total_users'  => $teamUsers->count(),
            'users'        => $teamUsers->take(3),      // 👈 first 3 users
            'extra_count'  => max(0, $teamUsers->count() - 3),
        ];
    });

  // =====================================================
    // ✅ DESIGNATION WISE DATA (ACTIVE USERS ONLY)
    // =====================================================
    $designationData = Designation::orderBy('name')->get()->map(function ($designation) use ($users) {

        // sirf active users + same designation
        $designationUsers = $users
            ->where('status', 1)
            ->where('designation', $designation->id)
            ->values();

        return [
            'id'           => $designation->id,
            'name'         => $designation->name,
            'total_users'  => $designationUsers->count(),
            'users'        => $designationUsers->take(4),   // 👈 first 4 active users
            'extra_count'  => max(0, $designationUsers->count() - 4),
        ];
    });


    $designationWiseUsers = $users->groupBy('designation');
    $role = Auth::user()->role ?? null;

    return view('FrontEnd.organization.show', compact(
        'organization',
        'teams',
        'teamData',
        'designationData',
        'designationWiseUsers',
         'users',
       'role'
    ));
}


 

    public function edit($id)
    {
        return response()->json(Organization::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
     $organization = Organization::findOrFail($id);
    $oldData = $organization->toArray();

    $user   = \Sentinel::check();
    $userId = $user->id;

    $reg   = registeruser($userId);
    $actorId = $reg->id;
    $role  = $user->roles()->first()->slug ?? null;

    // =========================
    // ✅ VALIDATION (ROLE BASED)
    // =========================
    if ($role === 'admin') {
        $validated = $request->validate([
            'name'=>'required',
            'address'=>'nullable',
            'city'=>'nullable',
            'state'=>'nullable',
            'country'=>'nullable',
            'zipcode'=>'nullable',
            'status'=>'required',
            'logo'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

      

    } else {
        $validated = $request->validate([
            'name'=>'required',
            'mobile'=>'required',
            'address'=>'nullable',
            'city'=>'nullable',
            'state'=>'nullable',
            'country'=>'nullable',
            'zipcode'=>'nullable',
            'gst'=>'nullable|string|max:20',
            'status'=>'required',
            'logo'=>'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
	}
        if ($request->hasFile('logo')) {
			$file = $request->file('logo');
			$filename = time() . '_' . $file->getClientOriginalName();
			// ✅ Correct folder
			$destinationPath = public_path('uploads/organization');
			// folder exists check (safe)
			if (!file_exists($destinationPath)) {
				mkdir($destinationPath, 0755, true);
			}
			// move file
			$file->move($destinationPath, $filename);
			// ✅ DB me relative path save karo
			$validated['logo'] = 'uploads/organization/' . $filename;
		}


        $validated['email'] = $organization->email;

        $organization->update($validated);
		
		
		
		$user   = \Sentinel::check();
        $userId = $user->id;

        $reg   = registeruser($userId);
        $orgId = $reg->organization;
        $actorId = $reg->id;
        

        logUserActivity($organization->id,$actorId,'organization',$organization->id,'update_organization',"Organization updated",['old'=>$oldData,'new'=>$validated]);
        logOrganizationActivity($organization->id,$actorId,'organization',$organization->id,'update_organization',"Organization details updated",['old'=>$oldData,'new'=>$validated]);

        if ($request->ajax()) {
            return response()->json(['status'=>'success','message'=>'Organization updated','organization'=>$organization]);
        }

        return response()->json(['success'=>true]);
    }

    public function destroy(string $id)
    {
        // SAME — NO CHANGE
        $organization = Organization::findOrFail($id);
        $actor = \Sentinel::check();
        $actorId = $actor->id;

        logUserActivity($organization->id,$actorId,'organization',$organization->id,'delete_organization',"Organization '{$organization->name}' deleted");
        logOrganizationActivity($organization->id,$actorId,'organization',$organization->id,'delete_organization',"Organization '{$organization->name}' removed");

        $organization->delete();

        return response()->json(['success'=>true]);
    }
}
