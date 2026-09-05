<?php
namespace App\Http\Controllers\FrontEnd;
 use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Sentinel;
use App\User;
use Illuminate\Support\Facades\DB;
use Activation;
use App\Client;
use Session;
use Auth;
use Route;
use Hash;
use Mail;
use Carbon\Carbon;
use App\Register;
use Excel;
use App\Organization;
use App\Designation;
use App\Role;
use App\Team;
use App\UsersImport;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Crypt;
class UserController extends Controller
{

    // -----------------------
    // Helper: get current actor and role slug + org
    // -----------------------
    private function actor()
    {
        return Sentinel::check();
    }

    private function actorRoleSlug()
    {
        $u = $this->actor();
        return $u ? ($u->roles()->first()->slug ?? null) : null;
    }

    private function actorOrgId()
    {
        $u = $this->actor();
        if (!$u) return null;
        $reg = registeruser($u->id);
        return $reg ? $reg->organization : null;
    }

    // -----------------------
    // Permission helpers
    // -----------------------
    private function canViewAnyOrganizationData()
    {
        $role = $this->actorRoleSlug();
        return in_array($role, ['root', 'sadmin']);
    }

    private function canAddOrganization() // only root + sadmin
    {
        $role = $this->actorRoleSlug();
        return in_array($role, ['root', 'sadmin']);
    }

    private function canDeleteUsers()
    {
        $role = $this->actorRoleSlug();
        // root can delete anybody; admin can delete inside their org
        return in_array($role, ['root', 'admin']);
    }

    private function canEditOrganizationUsers($targetOrgId)
    {
        $role = $this->actorRoleSlug();
        $actorOrg = $this->actorOrgId();

        if ($role === 'root' || $role === 'sadmin') return true;
        if (in_array($role, ['admin','manager'])) {
            return $actorOrg && $actorOrg == $targetOrgId;
        }
        // operater can edit own record only (handled separately)
        return false;
    }

    private function canViewUserRecord($targetUserId, $targetOrgId = null)
    {
        $role = $this->actorRoleSlug();
        $actor = $this->actor();
        $actorOrg = $this->actorOrgId();

        if ($role === 'root' || $role === 'sadmin') return true;
        if ($role === 'admin' || $role === 'manager') {
            return $actorOrg && $actorOrg == $targetOrgId;
        }
        if ($role === 'operater') {
            // operater can view only their own user record
            return $actor && $actor->id == $targetUserId;
        }
        return false;
    }

    // -----------------------
    // LIST USERS (VIEW page)
    // -----------------------
 public function listUser()
{
    $user   = $this->actor();
    $userId = $user ? $user->id : null;

    $reg   = registeruser($userId);
    $orgId = $reg ? $reg->organization : null;

    $role  = $this->actorRoleSlug();

    // =========================================================
    // ROLE DROPDOWN (apne se neeche wale roles)
    // =========================================================
    $currentRole   = Role::where('slug', $role)->first();
    $currentRoleId = $currentRole?->id;

    $rolesfordrop = ($currentRoleId !== null)
        ? Role::where('id', '>=', $currentRoleId)->get()
        : collect();

    // =========================================================
    // BASE QUERY (role wise)
    // =========================================================

    if (in_array($role, ['root', 'sadmin'])) {

        $users = DB::table('users')
            ->join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('registers', 'users.id', '=', 'registers.user_id')
            ->join('organizations', 'registers.organization', '=', 'organizations.id')
            ->leftJoin('designation', 'registers.designation', '=', 'designation.id')
            ->select(
                'users.*',
                'registers.id as rid',
                'registers.mobile',
                'registers.avatar',
                'registers.organization',
                'registers.first_name',
                'registers.last_name',
                'organizations.name as organization_name',
                'designation.name as designation_name',
                'roles.name as role_name',
                'role_users.role_id'
            );

        $organizations = Organization::all();
        $designations  = Designation::all();
        $roles         = $rolesfordrop;
        $teams         = Team::all();
    }

    elseif (in_array($role, ['admin', 'manager'])) {

        $users = DB::table('users')
            ->join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('registers', 'users.id', '=', 'registers.user_id')
            ->join('organizations', 'registers.organization', '=', 'organizations.id')
            ->leftJoin('designation', 'registers.designation', '=', 'designation.id')
            ->where('registers.organization', $orgId)
            ->select(
                'users.*',
                'registers.id as rid',
                'registers.mobile',
                'registers.avatar',
                'registers.first_name',
                'registers.last_name',
                'registers.organization',
                'organizations.name as organization_name',
                'designation.name as designation_name',
                'roles.name as role_name',
                'role_users.role_id'
            );

        $organizations = Organization::where('id', $orgId)->get();
        $designations  = Designation::where('organization_id', $orgId)->get();
        $roles         = $rolesfordrop;
        $teams         = Team::where('organization_id', $orgId)->get();
    }

    else {

        $users = DB::table('users')
            ->join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('registers', 'users.id', '=', 'registers.user_id')
            ->leftJoin('designation', 'registers.designation', '=', 'designation.id')
            ->where('users.id', $userId)
            ->select(
                'users.*',
                'registers.id as rid',
                'registers.mobile',
                'registers.first_name',
                'registers.last_name',
                'registers.avatar',
                'registers.organization',
                'designation.name as designation_name',
                'roles.name as role_name',
                'role_users.role_id'
            );

        $organizations = $orgId ? Organization::where('id', $orgId)->get() : collect();
        $designations  = $orgId ? Designation::where('organization_id', $orgId)->get() : collect();
        $roles         = $rolesfordrop;
        $teams         = Team::where('organization_id', $orgId)->get();
    }

    // =========================================================
    // COMMON FILTERS
    // =========================================================

    if (request('user_id')) {
        $users->where('users.id', request('user_id'));
    }

    if (request('name')) {
        $users->where(function ($q) {
            $q->where('registers.first_name', 'like', '%' . request('name') . '%')
              ->orWhere('registers.last_name', 'like', '%' . request('name') . '%');
        });
    }

    if (request('email')) {
        $users->where('users.email', 'like', '%' . request('email') . '%');
    }

    if (request('mobile')) {
        $users->where('registers.mobile', 'like', '%' . request('mobile') . '%');
    }

    if (request('organization')) {
        $users->where('registers.organization', request('organization'));
    }

    if (request('designation')) {
        $users->where('registers.designation', request('designation'));
    }

    if (request('team')) {
        $users->where('registers.team', request('team'));
    }

    if (request('status') !== null && request('status') !== '') {
        $users->where('registers.status', request('status'));
    }

    // =========================================================
    // FINAL
    // =========================================================

    $users = $users->orderBy('users.id', 'DESC')->get();

    return view(
        'FrontEnd.user',
        compact('users', 'organizations', 'designations', 'roles', 'teams', 'role', 'orgId')
    );
}

    // -----------------------
    // LIST USERS AJAX (Datatables)
    // -----------------------
    public function listUserAjax(Request $request)
    {
        $actor = $this->actor();
        $actorRole = $this->actorRoleSlug();
        $actorOrg = $this->actorOrgId();

        $columns = array(
            0 =>'first_name',
            1 =>'last_name',
            2 =>'email',
            3 =>'name',
            4 =>'last_login',
            5 =>'created_at',
            6 =>'id',
        );

        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        // Base query includes register.organization for permission checks in action column
        $baseQuery = DB::table('users')
            ->join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->join('registers', 'users.id', '=', 'registers.user_id')
            ->select('users.*', 'roles.name as role_name', 'role_users.role_id', 'registers.organization as org_id');

        // Apply server-side role filters
        if (in_array($actorRole, ['root','sadmin'])) {
            $posts = $baseQuery->orderBy($order,$dir);
        } elseif (in_array($actorRole, ['admin','manager'])) {
            $posts = $baseQuery->where('registers.organization', $actorOrg)->orderBy($order,$dir);
        } else {
            // operater or others - only own record
            $posts = $baseQuery->where('users.id', $actor->id)->orderBy($order,$dir);
        }

        // Search
        if(!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $posts = $posts->where(function($q) use ($search) {
                $q->where('users.id','LIKE',"%{$search}%")
                    ->orWhere('users.email', 'LIKE',"%{$search}%")
                    ->orWhere('users.first_name', 'LIKE',"%{$search}%")
                    ->orWhere('users.last_name', 'LIKE',"%{$search}%")
                    ->orWhere('users.last_login', 'LIKE',"%{$search}%")
                    ->orWhere('roles.name', 'LIKE',"%{$search}%")
                    ->orWhere('users.created_at', 'LIKE',"%{$search}%");
            });
        }

        return Datatables::of($posts)
            ->addColumn('action', function ($posts) use ($actor, $actorRole, $actorOrg) {
                $del = '';
                $st = '';
                $ed = '';
                $pr = '';

                // Status toggle (Activate/Deactivate) - allowed for roles with permission
                if ($posts->status == 1) {
                    if (Sentinel::getUser()->hasAccess(['User.Activate'])) {
                        $st = '<img id="' . $posts->id . ' active" class="pub_unpub" src ="' . asset('backend/assets/images/tick-circle.gif') . '" width = "16" height = "16" border = "0" title = "Make Active" />';
                    }
                } else {
                    if (Sentinel::getUser()->hasAccess(['User.Deactivate'])) {
                        $st = '<img id="' . $posts->id . ' inactive" class="pub_unpub" src = "' . asset('backend/assets/images/unpublish.gif') . '" width = "16" height = "16" border = "0" title = "Make Inactive" />';
                    }
                }

                // Edit - check permission and org-scope
                $canEdit = false;
                if (in_array($actorRole, ['root','sadmin'])) {
                    $canEdit = true;
                } elseif (in_array($actorRole, ['admin','manager'])) {
                    if ($actorOrg && $actorOrg == $posts->org_id) $canEdit = true;
                } elseif ($actorRole === 'operater') {
                    if ($actor && $actor->id == $posts->id) $canEdit = true; // own profile
                }

                if ($canEdit && Sentinel::getUser()->hasAccess(['User.EditUser'])) {
                    $ed = ' <a href = "' . env('APP_URL') . '/user/edit/' . $posts->id . '" ><img src = "' . asset('backend/assets/images/pencil.gif') . '" width = "16" height = "16" border = "0" /></a >';
                }

                // Delete - only root and admin (admin only within org)
                $canDelete = false;
                if ($actorRole === 'root') {
                    $canDelete = true;
                } elseif ($actorRole === 'admin' && $actorOrg && $actorOrg == $posts->org_id) {
                    $canDelete = true;
                }

                if ($canDelete && Sentinel::getUser()->hasAccess(['User.Destroy'])) {
                    $del = '<a href = "'.env('APP_URL') .'/user/'.$posts->id.'/destroy" onClick = "return confirm(\'Are you sure you want to delete record!\');" ><i class="fa fa-trash-o fa-fw" ></i ></a >';
                }

                return '<input name="chkid['.$posts->id .']" id="chkid" type="checkbox" value="'.$posts->id.'" class="checkbtn"  /> &nbsp;'.$st.$ed.$pr.$del;
            })
            ->make(true);
    }

    // -----------------------
    // ActionUser (simple list) - keep as is but add permission filter
    // -----------------------
    public function actionUser()
    {
        $actorRole = $this->actorRoleSlug();
        $actorOrg = $this->actorOrgId();

        $query = DB::table('users')
            ->join('role_users', 'users.id', '=', 'role_users.user_id')
            ->join('roles', 'roles.id', '=', 'role_users.role_id')
            ->select('users.*', 'roles.name', 'role_users.role_id');

        if (!in_array($actorRole, ['root', 'sadmin'])) {
            // admin/manager see only their org or own user for operater
            if (in_array($actorRole, ['admin','manager'])) {
                $query->join('registers', 'users.id', '=', 'registers.user_id')
                      ->where('registers.organization', $actorOrg);
            } else {
                $query->where('users.id', $this->actor()->id);
            }
        }

        $userlist = $query->get();
        return view('FrontEnd.user', ['users'=>$userlist]);
    }

    // -----------------------
    // BULK STATUS / DELETE
    // -----------------------
    public function statusUser(Request $request)
    {
        $actor = $this->actor();
        $actorReg = registeruser($actor->id);
        $actorOrg = $actorReg->organization;
        $actorRole = $this->actorRoleSlug();

        if($request->action=="Active")
        {
            foreach ($request->chkid as $id)
            {
                // permission: can the actor edit/activate this user?
                $targetReg = Register::where('user_id', $id)->first();
                $targetOrg = $targetReg ? $targetReg->organization : null;

                if (!$this->canEditOrganizationUsers($targetOrg) && !($actor && $actor->id == $id)) {
                    continue; // skip unauthorized
                }

                $user = Sentinel::findById($id);
                $user->update(['status' => '1']);
                Register::where('user_id', $id)->update(['status' => 1]);

                logUserActivity($actorOrg, $actor->id, 'user', $id, 'activate_user', "User activated");
                logOrganizationActivity($actorOrg, $actor->id, 'user', $id, 'activate_user', "Organization member activated");
            }
            return redirect('user')->with('success', 'User Status Updated Successfully');
        }

        if($request->action=="Inactive")
        {
            foreach ($request->chkid as $id)
            {
                $targetReg = Register::where('user_id', $id)->first();
                $targetOrg = $targetReg ? $targetReg->organization : null;

                if (!$this->canEditOrganizationUsers($targetOrg) && !($actor && $actor->id == $id)) {
                    continue; // skip unauthorized
                }

                Sentinel::findById($id)->update(['status' => '0']);
                Register::where('user_id', $id)->update(['status' => 0]);

                logUserActivity($actorOrg, $actor->id, 'user', $id, 'deactivate_user', "User deactivated");
                logOrganizationActivity($actorOrg, $actor->id, 'user', $id, 'deactivate_user', "Organization member deactivated");
            }
            return redirect('user')->with('success', 'User Status Updated Successfully');
        }

        if($request->action=="Delete")
        {
            // Only root or admin (within org) allowed to delete
            foreach ($request->chkid as $id)
            {
                $targetReg = Register::where('user_id', $id)->first();
                $targetOrg = $targetReg ? $targetReg->organization : null;

                // authorize delete
                $allowDelete = false;
                if ($actorRole === 'root') $allowDelete = true;
                if ($actorRole === 'admin' && $actorOrg == $targetOrg) $allowDelete = true;
                if (!$allowDelete) continue; // skip unauthorized

                User::where('id', $id)->delete();
                Register::where('user_id', $id)->delete();
                DB::table("role_users")->where('user_id', $id)->delete();

                logUserActivity($actorOrg, $actor->id, 'user', $id, 'delete_user', "User deleted");
                logOrganizationActivity($actorOrg, $actor->id, 'user', $id, 'delete_user', "Organization member deleted");
            }

            return redirect('user')->with('success', 'User Delete Successfully');
        }
    }

    // -----------------------
    // Permissions view — keep as is
    // -----------------------
    public function permissions($id)
    {
        $user = Sentinel::findById($id);

        $routes = Route::getRoutes();
        $actions = [];
        foreach ($routes as $route) {
            if ($route->getName() != "" && !substr_count($route->getName(), 'payment')) {
                $actions[] = $route->getName();
            }
        }

        // remove store option
        $input = preg_quote("store", '~');
        $var = preg_grep('~' . $input . '~', $actions);
        $actions = array_values(array_diff($actions, $var));

        // remove update option
        $input = preg_quote("update", '~');
        $var = preg_grep('~' . $input . '~', $actions);
        $actions = array_values(array_diff($actions, $var));

        $var = [];
        $i = 0;
        foreach ($actions as $action) {
            $input = preg_quote(explode('.', $action)[0] . ".", '~');
            $var[$i] = preg_grep('~' . $input . '~', $actions);
            $actions = array_values(array_diff($actions, $var[$i]));
            $i += 1;
        }

        $actions = array_filter($var);

        return view('FrontEnd.permissions', compact('actions','user'));
    }

    // -----------------------
    // Save permissions — keep as is
    // -----------------------
    public function save($id, Request $request)
    {
        $user = Sentinel::findById($id);
        $user->permissions = [];
        if($request->permissions){
            foreach ($request->permissions as $permission) {
                if(explode('.', $permission)[1] == 'create'){
                    $user->addPermission($permission);
                    $user->addPermission(explode('.', $permission)[0].".store");
                }
                else if(explode('.', $permission)[1] == 'edit'){
                    $user->addPermission($permission);
                    $user->addPermission(explode('.', $permission)[0].".update");
                }
                else{
                    $user->addPermission($permission);
                }
            }
        }

        $user->save();

        return redirect('user')->with('success', 'Success! Permissions are stored successfully.');
    }

    // -----------------------
    // ACTIVATE / DEACTIVATE single user
    // -----------------------
    public function activate(Request $request,$id)
    {
        $actor = $this->actor();
        $actorOrg = $this->actorOrgId();
        $actorRole = $this->actorRoleSlug();

        $targetReg = Register::where('user_id', $id)->first();
        $targetOrg = $targetReg ? $targetReg->organization : null;

        // permission check: actor must be allowed to edit target
        if (!$this->canEditOrganizationUsers($targetOrg) && !($actor && $actor->id == $id)) {
            abort(403, 'Unauthorized');
        }

        $user = Sentinel::findById($id);
        $user->update(array('status' => '1'));

        if ($targetReg) {
            $targetReg->update(array('status' => '1'));
        }

        return redirect('user')->with('success', 'Success! The user is activated successfully.');
    }

    public function deactivate(Request $request,$id)
    {
        $actor = $this->actor();
        $actorOrg = $this->actorOrgId();

        $targetReg = Register::where('user_id', $id)->first();
        $targetOrg = $targetReg ? $targetReg->organization : null;

        if (!$this->canEditOrganizationUsers($targetOrg) && !($actor && $actor->id == $id)) {
            abort(403, 'Unauthorized');
        }

        $user = Sentinel::findById($id);
        $user->update(array('status' => '0'));

        if ($targetReg) {
            $targetReg->update(array('status' => '0'));
        }

        return redirect('user')->with('success', 'Success! The user is deactivated successfully.');
    }

    // -----------------------
    // Destroy single user
    // -----------------------
    public function destroy(Request $request,$id)
    {
        $actor = $this->actor();
        $actorOrg = $this->actorOrgId();
        $actorRole = $this->actorRoleSlug();

        $targetReg = Register::where('user_id', $id)->first();
        $targetOrg = $targetReg ? $targetReg->organization : null;

        // Only root or admin (within org) allowed to delete
        $allowDelete = false;
        if ($actorRole === 'root') $allowDelete = true;
        if ($actorRole === 'admin' && $actorOrg == $targetOrg) $allowDelete = true;
        if (!$allowDelete) abort(403, 'Unauthorized');

        $user = User::findOrFail($id);
        $user->delete();

        if ($targetReg) {
            Register::where('user_id', $id)->delete();
        }

        DB::table("role_users")->where('user_id', $id)->delete();

        return redirect('user')->with('success', 'Success! User is deleted successfully.');
    }

    // -----------------------
    // Add form
    // -----------------------
    public function Add()
    {
        $actorRole = $this->actorRoleSlug();
        // Organizations listing: root/sadmin see all; admin/manager see only their org
        if (in_array($actorRole, ['root','sadmin'])) {
            $organizations = Organization::all();
        } else {
            $orgId = $this->actorOrgId();
            $organizations = $orgId ? Organization::where('id', $orgId)->get() : collect();
        }
        $designations = Designation::all();
        return view('FrontEnd.add-edit-user', compact('organizations', 'designations'));
    }

    // -----------------------
    // AddUser (create)
    // -----------------------
public function AddUser(Request $request)
{
    try {

        $request->validate([
            'first_name'   => 'required',
            'last_name'    => 'required',
            'email'        => 'required|email|unique:users,email',
            'mobile'       => 'required',
            'password'     => 'required|min:8|confirmed',
            'organization' => 'required', // array or single
            'role'         => 'required'
        ], [
            'password.confirmed' => 'Password and Confirm Password do not match.',
        ]);

    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'validation_error',
            'errors' => $e->errors()
        ], 422);
    }

    // ------------------------------------------------
    // Actor Info
    // ------------------------------------------------
    $actor     = $this->actor();
    $actorRole = $this->actorRoleSlug();
    $actorOrg  = session('organization'); // 🔥 current session org

    // ------------------------------------------------
    // Normalize organization input
    // ------------------------------------------------
    if (is_array($request->organization)) {
        $selectedOrgs = $request->organization;
    } else {
        $selectedOrgs = [$request->organization];
    }

    // comma string बनाओ
    $otherOrgString = implode(',', $selectedOrgs);

    // ------------------------------------------------
    // Permission check (admin multi org support)
    // ------------------------------------------------
    if (!in_array($actorRole, ['root', 'sadmin', 'admin'])) {

        $allowedOrgs = explode(',', $actor->other_org ?? '');

        foreach ($selectedOrgs as $orgId) {
            if (!in_array($orgId, $allowedOrgs)) {
                abort(403, 'Unauthorized to create user in this organization.');
            }
        }
    }

    // ------------------------------------------------
    // STEP 1: Sentinel user create
    // ------------------------------------------------
    $user = Sentinel::registerAndActivate([
        'email'      => $request->email,
        'password'   => $request->password,
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
    ]);

    // ------------------------------------------------
    // STEP 2: Avatar upload
    // ------------------------------------------------
    $avatar = null;
    if ($request->hasFile('avatar')) {
        $file = $request->file('avatar');
        $avatar = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/users'), $avatar);
    }

    // ------------------------------------------------
    // STEP 3: Register table insert
    // ------------------------------------------------
    Register::create([
        'user_id'      => $user->id,
        'avatar'       => $avatar,
        'user_type'    => $request->role,
        'first_name'   => $request->first_name,
        'last_name'    => $request->last_name,
        'email'        => $request->email,
        'mobile'       => $request->mobile,
        'user_name'    => $request->user_name ?? $request->email,
        'address'      => $request->address,
        'notes'        => $request->notes,

        // 🔥 MAIN LOGIC
        'organization' => $actorOrg,        // session वाला org
        'other_org'    => $otherOrgString,  // selected orgs

        'designation'  => $request->designation,
        'team'         => $request->team,
        'role'         => $request->role,
        'status'       => 1
    ]);

    // ------------------------------------------------
    // STEP 4: Attach role
    // ------------------------------------------------
    DB::table('role_users')->insert([
        'user_id'    => $user->id,
        'role_id'    => $request->role,
        'created_at'=> now(),
        'updated_at'=> now()
    ]);

    // ------------------------------------------------
    // Logs
    // ------------------------------------------------
    logUserActivity(
        $actorOrg,
        $actor->id,
        'user',
        $user->id,
        'create_user',
        "User {$user->email} created successfully",
        ['created_by' => $actor->email]
    );

    logOrganizationActivity(
        $actorOrg,
        $actor->id,
        'user',
        $user->id,
        'create_user',
        "Organization member '{$user->email}' created",
        ['role' => $request->role, 'team' => $request->team]
    );

    return response()->json([
        'status'  => 'success',
        'message' => 'User created successfully!'
    ]);
}


    // -----------------------
    // Edit user (update)
    // -----------------------
public function editUser(Request $request, $id)
{
    $regUser = Register::where('user_id', $id)->firstOrFail();

    // ------------------------------------------------
    // Validation
    // ------------------------------------------------
    $request->validate([
        'first_name' => 'required',
        'last_name'  => 'required',
        'email'      => 'required|email|unique:users,email,' . $regUser->user_id,
        'mobile'     => 'required',
        'password'   => 'nullable|min:8|confirmed',
        'role'       => 'required',
        'organization' => 'required' // array or single
    ]);

    // ------------------------------------------------
    // Actor Info
    // ------------------------------------------------
    $actor     = $this->actor();
    $actorRole = $this->actorRoleSlug();
    $actorOrg  = session('organization'); // 🔥 current session org

    // ------------------------------------------------
    // Normalize organization
    // ------------------------------------------------
    if (is_array($request->organization)) {
        $selectedOrgs = $request->organization;
    } else {
        $selectedOrgs = [$request->organization];
    }

    $otherOrgString = implode(',', $selectedOrgs);

    // ------------------------------------------------
    // Permission check (multi-org support)
    // ------------------------------------------------
    if (!in_array($actorRole, ['root', 'sadmin', 'admin'])) {

        $allowedOrgs = explode(',', $actor->other_org ?? '');

        foreach ($selectedOrgs as $orgId) {
            if (!in_array($orgId, $allowedOrgs)) {
                abort(403, 'Unauthorized to edit this user.');
            }
        }
    }

    // OLD data
    $oldData = $regUser->toArray();

    // ------------------------------------------------
    // Update REGISTER table
    // ------------------------------------------------
    $regUser->update([
        'first_name'   => $request->first_name,
        'last_name'    => $request->last_name,
        'email'        => $request->email,
        'mobile'       => $request->mobile,

        // 🔥 MAIN CHANGE
        'organization' => $actorOrg,        // session org
        'other_org'    => $otherOrgString,  // selected orgs

        'designation'  => $request->designation,
        'team'         => $request->team,
        'role'         => $request->role,
        'status'       => $request->status,
    ]);

    // ------------------------------------------------
    // Avatar
    // ------------------------------------------------
    if ($request->hasFile('avatar')) {
        $file = $request->file('avatar');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/users'), $filename);
        $regUser->update(['avatar' => $filename]);
    }

    // ------------------------------------------------
    // Update USERS table
    // ------------------------------------------------
    $user = Sentinel::findById($regUser->user_id);

    $updateData = [
        'email'      => $request->email,
        'first_name' => $request->first_name,
        'last_name'  => $request->last_name,
    ];

    if ($request->filled('password')) {
        $updateData['password'] = $request->password;
    }

    Sentinel::update($user, $updateData);

    // ------------------------------------------------
    // Update role
    // ------------------------------------------------
    DB::table('role_users')->where('user_id', $user->id)->delete();
    DB::table('role_users')->insert([
        'user_id'    => $user->id,
        'role_id'    => $request->role,
        'created_at'=> now(),
        'updated_at'=> now()
    ]);

    // ------------------------------------------------
    // Logs
    // ------------------------------------------------
    logUserActivity(
        $actorOrg,
        $actor->id,
        'user',
        $user->id,
        'update_user',
        "User {$user->email} updated",
        ['old' => $oldData, 'new' => $request->all()]
    );

    logOrganizationActivity(
        $actorOrg,
        $actor->id,
        'user',
        $user->id,
        'update_user',
        "Organization member {$user->email} updated",
        ['changes' => $request->all()]
    );

    return response()->json([
        'status'  => 'success',
        'message' => 'User updated successfully!'
    ]);
}



public function assignOrganization(Request $request)
{
    $request->validate([
        'user_id' => 'required',
        'organization' => 'required',
        'team' => 'required',
        'designation' => 'required'
    ]);

    $reg = Register::where('user_id', $request->user_id)->firstOrFail();

    // existing orgs
    $existing = $reg->other_org 
        ? explode(',', $reg->other_org) 
        : [];

    if (!in_array($request->organization, $existing)) {
        $existing[] = $request->organization;
    }

    $reg->update([
        'organization' => $request->organization,
        'other_org' => implode(',', $existing),
        'team' => $request->team,
        'designation' => $request->designation
    ]);

    return response()->json([
        'status' => 'success',
        'message' => 'Organization Assigned Successfully'
    ]);
}





// -----------------------
    // getUserData (AJAX)
    // -----------------------
    public function getUserData($id)
    {
        $user =  Register::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.'
            ], 404);
        }

        $actor = $this->actor();
        $actorRole = $this->actorRoleSlug();
        $actorOrg = $this->actorOrgId();

        // permission: can actor view this user?
        if (!$this->canViewUserRecord($user->user_id, $user->organization)) {
            return response()->json(['status'=>'error','message'=>'Unauthorized'], 403);
        }

        $role = DB::table('role_users')->where('user_id', $user->user_id)->first();
        $role_name = DB::table('roles')->where('id', $user->role)->value('name as role_name');
        $team = DB::table('teams')->where('id', $user->team)->value('name as team');

        return response()->json([
          'status' => 'success',
			'id'              => $user->user_id,
			'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'organization' => $user->organization,
            'designation' => $user->designation,
            'avatar' => $user->avatar,
             'team'            => $user->team,
			'role'            => $user->role,
			'status'          => $user->status,
			'avatar'          => $user->avatar,
        ]);
    }

    // -----------------------
    // SHOW PROFILE / Logs
    // -----------------------
    public function show($id)
    {
        try {
        $id = Crypt::decrypt($id);
    } catch (\Exception $e) {
        abort(404);
    }

	// -----------------------
        // 1. Load User register record
        // -----------------------
        $user =  Register::findOrFail($id);

        // permission: can current actor view?
        if (!$this->canViewUserRecord($user->user_id, $user->organization)) {
            abort(403, 'Unauthorized');
        }

        // -----------------------
        // 2. User Activity Logs
        // -----------------------
        $userLogs = \DB::table('user_activity_logs')
            ->where('user_id', $user->user_id)
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();

        // -----------------------
        // 3. Organization Activity Logs
        // -----------------------
        $orgLogs = \DB::table('org_activity_logs')
            ->where('organization_id', $user->organization)
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();

        // -----------------------
        // 4. Lead Activity Logs
        // -----------------------
        $leadLogs = \DB::table('lead_activity_logs')
            ->where('user_id', $user->user_id)
            ->orderBy('id', 'DESC')
            ->limit(50)
            ->get();

        return view('FrontEnd.user.profile', compact(
            'user',
            'userLogs',
            'orgLogs',
            'leadLogs'
        ));
    }





}
