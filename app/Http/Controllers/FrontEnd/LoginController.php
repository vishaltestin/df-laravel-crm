<?php
namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Sentinel;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use App\User;
use App\GeneralSetting;
use App\Register;

use Illuminate\Support\Facades\Session;
/**
 * Class LoginController
 * @package App\Http\Controllers\FrontEnd
 */

class LoginController extends Controller
{
    public function index()
    {

        if(Sentinel::check())
        {
            return redirect('./dashboard');
        }
      return \View::make('FrontEnd.welcome');
    }


   public function postLogin(Request $request)
{
    try {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            
            // ❌ Invalid Fields — USER ATTEMPT LOG
            logUserActivity(
                null,                         // org id unknown
                null,                         // user id unknown
                'auth',                       // entity
                null,
                'login_failed',
                'Login failed due to validation errors',
                ['email' => $request->email]
            );

            return redirect('./dashboard')
                ->withErrors($validator)
                ->withInput();
        }

        // Attempt login
        $user = Sentinel::authenticate($request->all());

        Session::put('language', 'en');

        if (Sentinel::check()) {

            $loggedUser = Sentinel::getUser();

            // Organization ID (from register table)
            $reg = registeruser($loggedUser->id);
            $orgId = $reg ? $reg->organization : null;
			$avatar = $reg ? $reg->avatar : null;
			Session::put('organization', $orgId);
			$actorId=$reg->id;
			
			
			
			$currentOrg = $reg->organization;

				// other_org ko array me convert karo
				$otherOrgs = $reg->other_org 
					? explode(',', $reg->other_org) 
					: [];

				// current org ko bhi include karo (important 🔥)
				$allOrgIds = array_unique(array_merge([$currentOrg], $otherOrgs));

				// organization table se data fetch
				$orgData = \App\Organization::whereIn('id', $allOrgIds)
							->get(['id','name','logo'])
							->toArray();

				// ids alag nikaal lo
				$orgIds = array_column($orgData, 'id');

				// session me daalo
				Session::put('organizations', $orgIds);
				Session::put('organization_data', $orgData);

$institutes = \App\Institute::where('organization_id', $orgId)
                ->where('status', 1)
                ->get(['id','name'])
                ->toArray();
 $encodedInstitutes = [];

foreach ($institutes as $inst) {
    $inst['enc_id'] = encrypt($inst['id']); // 🔐 encode id
    $encodedInstitutes[] = $inst;
}

// session me store
Session::put('institutes', $encodedInstitutes);

// default current institute (first one)
Session::put('current_institute',   null);

$currentInstitute =   null;
Session::put('current_institute_data', $currentInstitute);


				// Store avatar in session
				Session::put('avatar', $avatar);
            // ✅ Successful Login Log
            logUserActivity(
                $orgId,
                $loggedUser->id,
                'auth',
                $loggedUser->id,
                'login_success',
                "User {$loggedUser->email} logged in successfully",
                ['ip' => request()->ip()]
            );
 
		$setting =  GeneralSetting::where('organization_id', $orgId)->first();

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

            return redirect('dashboard');

        } else {

            // ❌ Wrong credentials log
            logUserActivity(
                null,
                null,
                'auth',
                null,
                'login_failed',
                "Failed login attempt for email {$request->email}",
                ['ip' => request()->ip()]
            );

            return redirect()->back()->with(['error' => 'Wrong Credentials']);
        }
    }
    catch (ThrottlingException $exception)
    {
        $delay = $exception->getDelay();

        // ⛔ Banned user log
        logUserActivity(
            null,
            null,
            'auth',
            null,
            'login_banned',
            "User temporary banned for {$delay} seconds",
            ['ip' => request()->ip()]
        );

        return redirect()->back()->with(['error' => "You are banned for $delay Seconds"]);
    }
}

public function switchInstitute($id)
{
    try {
        $id = decrypt($id);

        $institutes = Session::get('institutes', []);

        if (!collect($institutes)->pluck('id')->contains($id)) {
            return back()->with('error', 'Invalid Institute');
        }

        Session::put('current_institute', $id);

        $currentInstitute = collect($institutes)->firstWhere('id', $id);
        Session::put('current_institute_data', $currentInstitute);

        return back(); // 🔥 important (same page reload)

    } catch (\Exception $e) {
        return back()->with('error', 'Error');
    }
}public function clearInstitute()
{
    Session::forget('current_institute');
    Session::forget('current_institute_data');

    return back();
}


	public function logout()
	{
		if (Sentinel::check()) {

			$user = Sentinel::getUser();
			$reg = registeruser($user->id);
			$orgId = $reg ? $reg->organization : null;

			// 🔥 Logout Log
			logUserActivity(
				$orgId,
				$user->id,
				'auth',
				$user->id,
				'logout',
				"User {$user->email} logged out"
			);
		}

		Sentinel::logout();
		return redirect('./');
	}

public function forgotPasswordForm()
{
    return view('auth.forgot');
}
public function sendResetLink(Request $request)
{
    $user = \Sentinel::findByCredentials(['login' => $request->email]);

    if (!$user) {
        return back()->with('error', 'Email not found');
    }

    $reminder = \Reminder::exists($user) ?: \Reminder::create($user);

    $link = url('password/reset/' . $reminder->code);

    // Send email using your SMTP
    \Mail::raw("Click to reset password: " . $link, function ($msg) use ($request) {
        $msg->to($request->email)->subject("Password Reset");
    });

    return back()->with('success', 'Reset link has been sent to your email');
}
public function resetPasswordForm($code)
{
    return view('auth.reset', compact('code'));
}
public function resetPassword(Request $request, $code)
{
    $user = \Sentinel::findById($request->user_id);

    if (!$user) {
        return back()->with('error', 'User not found');
    }

    if (!\Reminder::complete($user, $code, $request->password)) {
        return back()->with('error', 'Invalid or expired reset code');
    }

    return redirect('/login')->with('success', 'Password reset successfully');
}


}