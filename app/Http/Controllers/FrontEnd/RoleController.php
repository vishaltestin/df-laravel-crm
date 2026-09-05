<?php
namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Sentinel;
use App\User;
use Illuminate\Support\Facades\DB;

use Activation;

use App\Role;
use Session;
use Auth;
use Route;
use Hash;
use Mail;
use Carbon\Carbon;

class RoleController extends Controller
{
    public function listRole()
    {
        //$user= User::all();

        $role = DB::table('roles')->get();

//dd($user);
        return 	view('FrontEnd.role',['roles'=>$role]);
    }



    public function rolepermission($id){
        $role = Sentinel::findRoleById($id);

        $routes = Route::getRoutes();


        $actions = [];
        foreach ($routes as $route) {
            if ($route->getName() != "" && !substr_count($route->getName(), 'payment')) {
                $actions[] = $route->getName();
            }
        }

        //remove store option
        $input = preg_quote("store", '~');
        $var = preg_grep('~' . $input . '~', $actions);
        $actions = array_values(array_diff($actions, $var));

        //remove update option
        $input = preg_quote("update", '~');
        $var = preg_grep('~' . $input . '~', $actions);
        $actions = array_values(array_diff($actions, $var));



        $var = [];
        $i = 0;
        foreach ($actions as $action) {

            $input = preg_quote(explode('.', $action )[0].".", '~');
            $var[$i] = preg_grep('~' . $input . '~', $actions);
            $actions = array_values(array_diff($actions, $var[$i]));
            $i += 1;
        }

        $actions = array_filter($var);
        // dd (array_filter($actions));
        return view('FrontEnd.rolepermission', compact('actions','role'));
    }

    public function save($id, Request $request){
        $role = Sentinel::findRoleById($id);
        $role->permissions = [];
        if($request->permissions){
            foreach ($request->permissions as $permission) {
                if(explode('.', $permission)[1] == 'create'){
                    $role->addPermission($permission);
                    $role->addPermission(explode('.', $permission)[0].".store");
                }
                else if(explode('.', $permission)[1] == 'edit'){
                    $role->addPermission($permission);
                    $role->addPermission(explode('.', $permission)[0].".update");
                }
                else{
                    $role->addPermission($permission);
                }
            }
        }

        $role->save();

        return redirect('role')->with('success', 'Success! Permissions are stored successfully.');
    }




    public function role()
    {
        return view('FrontEnd.add-edit-role');
    }


    public function postRole(Request $request)
    {
        $validator =Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);



        if($validator->fails())
        {
            return redirect('add-edit-role')
                ->withErrors($validator)
                ->withInput();
        }


        //dd(str_slug($request->name));

        DB::table('roles')->insert(array(array('slug'=>str_slug($request->slug), 'name'=>$request->name, 'created_at'=>now(), 'updated_at'=>now())));


        return redirect('role')->with('success', 'Role Added Successfully');
    }



    public function viewRole(Request $request , $id)
    {

        $role= DB::table('roles')->where('id', $id)->first();

        //pass posts data to view and load list view
        return view('FrontEnd.add-edit-role', ['roles' => $role]);
        //return 	view('backend.register');
    }


    public function editRole(Request $request , $id)
    {
        $validator =Validator::make($request->all(), [
            'name' => 'required|string|max:255',

        ]);

        if($validator->fails())
        {
            return redirect('add-edit-role/'.$id)
                ->withErrors($validator)
                ->withInput();
        }
        //update post data

        DB::table('roles')->where('id', $id)->update(array('name' => $request->name, 'updated_at'=>now()));


        return redirect('role')->with('success', 'Role Update Successfully');
    }




}
