<?php

namespace App;

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
use App\Moreabout;
use App\Connect;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {


        $uid = User::where('email', '=', $row[0])->first();


        if ($uid['id'] == '')
        {
            //dd($uid);

        $arr = ['email' => $row[0], 'password' => $row[2], 'first_name' => $row[3], 'last_name' => $row[4], 'location' => $row[5], 'status' => 1];
        $arr1 = ['Mobile' => $row[1], 'term' => 'Yes'];

        $credentials = [
            "_token" => csrf_token(),
            "first_name" => $row[3],
            "email" => $row[0],
            "location" => $row[5],
            "role" => "customer",
            "last_name" => $row[4],
            "password" => $row[2],
            "password_confirmation" => $row[2],
            "status" => "1"
        ];

        $user = Sentinel::registerAndActivate($credentials);
        $role = Sentinel::findRoleBySlug('customer');
        $role->users()->attach($user);

        $register['status'] = 1;
        $register['term'] = 'Yes';
        $register['user_id'] = $user->id;
        $register['profile'] = 'Yes';
        $register['user_type'] = 'Backend';
        $register['email'] = $row[0];
        $register['Mobile'] = $row[1];
        $register['City'] = $row[5];
        $register['State'] = $row[5];
        $register['First_Name'] = $user->first_name;
        $register['Last_Name'] = $user->last_name;
        $registers = Register::create($register);
        }

    }



}