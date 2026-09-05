<?php

namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\User;
use Reminder;
use Mail;
use Sentinel;
class ForgotPasswordController extends Controller
{
    public function forgotPassword()
    {
        return 	view('FrontEnd.forgot-password');
    }

    public function resetPassword($email, $resetCode)
    {

        $user=User::whereEmail($email)->first();


      if(count($user)==0)
          abort(404);
        $sentineluser=Sentinel::findById($user->id);
          if($reminder=Reminder::exists($sentineluser))
          {
              if($resetCode==$reminder->code)
                  return view('FrontEnd.reset-password');
              else
                  return redirect('./dashboard');
          }
          else
          {
              return redirect('./dashboard');
          }

    }


    public function postResetPassword(Request $request, $email, $resetCode)
    {

        $this->validate($request,[
            'password'=>'confirmed|required|min:5|max:10',
            'password_confirmation'=>'required|min:5|max:10'

        ]);

        $user=User::whereEmail($email)->first();

        if(count($user)==0)
            abort(404);
        $sentinelUser=Sentinel::findById($user->id);
        if($reminder=Reminder::exists($sentinelUser))
        {
            if($resetCode==$reminder->code) {
                //dd($resetCode);
                Reminder::Complete($sentinelUser, $resetCode, $request->password);

                return redirect('./')->with('success', 'Please Login with new password');
            }
        }
        else
        {
            return redirect('./');
        }
    }





    public function postForgotPassword(Request $request)
    {
        $user=User::whereEmail($request->email)->first();

        if(!$user) {
            return redirect()->back()->with(['error' => 'Wrong Email ID']);
        }

        $sentinelusers=Sentinel::findById($user->id);


        if(count($user) == 0)
        {
            return redirect()->back()->with(['success'=>'Reset Code was sent ypur email id.']);
        }


            $reminder=Reminder::exists($sentinelusers)?:Reminder::create($sentinelusers);
            $this->sendEmail($user,$reminder->code);
             return redirect()->back()->with(['success'=>'Reset Code was sent ypur email id.']);

    }

    private function sendEmail($user, $code)
    {
      Mail::send('FrontEnd/emails.forgot-password',[
         'user' => $user,
         'code' =>$code
      ], function($message) use ($user){
          $message->to($user->email);
          $message->subject("Hello $user->first_name, reset your password.");
    });
    }






}
