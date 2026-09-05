<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\OtpVerification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class OtpController extends Controller
{

    public function send(Request $request)
    {
        $request->validate([
            'organization_id' => 'required',
            'type'            => 'required|in:email,mobile',
            'recipient'       => 'required',
            'customer_name'   => 'nullable'
        ]);

        $orgId = $request->organization_id;

        // 🔥 Expire old unverified OTP
        OtpVerification::where([
            'organization_id' => $orgId,
            'type'            => $request->type,
            'recipient'       => $request->recipient,
            'is_verified'     => 0
        ])->update([
            'expires_at' => now()
        ]);

        $otp = rand(100000, 999999);

        // ✅ Save OTP
        OtpVerification::create([
            'organization_id' => $orgId,
            'type'            => $request->type,
            'recipient'       => $request->recipient,
            'otp_code'        => $otp,
            'is_verified'     => 0,
            'expires_at'      => Carbon::now()->addMinutes(5),
        ]);

        $variables = [
            '{otp}'            => $otp,
            '{customer_name}'  => $request->customer_name ?? 'User',
            '{customer_mobile}'=> $request->recipient
        ];

        // ===================================================
        // SEND SMS
        // ===================================================
        // ===================================================
// SEND SMS
// ===================================================
if($request->type === 'mobile'){

    $template = DB::table('message_templates')
        ->where('organization_id', $orgId)
        ->where('category', 'otp')
        ->where('status', 'active')
        ->latest()
        ->first();

    if(!$template){
        return response()->json([
            'status'=>false,
            'message'=>'OTP SMS template not found'
        ], 400);
    }

    $message = str_replace(
        array_keys($variables),
        array_values($variables),
        $template->message
    );

    $smsSettings = DB::table('sms_settings')
        ->where('organization_id', $orgId)
        ->where('status', 1)
        ->first();

    if(!$smsSettings){
        return response()->json([
            'status'=>false,
            'message'=>'SMS settings not configured'
        ], 400);
    }

    // 🔥 Mobile format fix (remove + and add 91 if missing)
    $mobile = ltrim($request->recipient, '+');
    if (!str_starts_with($mobile, '91')) {
        $mobile = '91' . $mobile;
    }

    // 🔥 Encode message
    $encodedMessage = urlencode($message);

    // 🔥 Proper URL build as per provider
    $url = $smsSettings->api_url
        . "?action=send-sms"
        . "&api_key=" . $smsSettings->api_key
        . "&to=" . $mobile
        . "&from=" . $smsSettings->sender_id
        . "&sms=" . $encodedMessage;

    try{

        $response = Http::get($url);

        logSmsActivity(
            $orgId,
            null,
            $mobile,
            $message,
            $smsSettings->provider,
            null,
            $response->successful() ? 'sent' : 'failed',
            $response->body(),
            null,
            $url
        );

    }catch(\Exception $e){

        logSmsActivity(
            $orgId,
            null,
            $mobile,
            $message,
            $smsSettings->provider,
            null,
            'failed',
            ['error'=>$e->getMessage()],
            null,
            $url
        );

        return response()->json([
            'status'=>false,
            'message'=>'SMS sending failed'
        ], 500);
    }
}

        // ===================================================
        // SEND EMAIL
        // ===================================================
        if($request->type === 'email'){

            $template = DB::table('email_templates')
                ->where('organization_id', $orgId)
                ->where('category', 'otp')
                ->where('status', 'active')
                ->latest()
                ->first();

            if(!$template){
                return response()->json([
                    'status'=>false,
                    'message'=>'OTP Email template not found'
                ], 400);
            }

            $subject = str_replace(
                array_keys($variables),
                array_values($variables),
                $template->subject
            );

            $body = str_replace(
                array_keys($variables),
                array_values($variables),
                $template->message
            );

            $emailSettings = DB::table('email_settings')
                ->where('organization_id', $orgId)
                ->where('status', 1)
                ->first();

            if(!$emailSettings){
                return response()->json([
                    'status'=>false,
                    'message'=>'Email settings not configured'
                ], 400);
            }

            Config::set('mail.mailers.smtp.host', $emailSettings->mail_host);
            Config::set('mail.mailers.smtp.port', $emailSettings->mail_port);
            Config::set('mail.mailers.smtp.username', $emailSettings->mail_username);
            Config::set('mail.mailers.smtp.password', $emailSettings->mail_password);
            Config::set('mail.mailers.smtp.encryption', $emailSettings->mail_encryption);
            Config::set('mail.from.address', $emailSettings->mail_from_address);
            Config::set('mail.from.name', $emailSettings->mail_from_name);

            try{

                Mail::html($body, function($mail) use ($request, $subject){
                    $mail->to($request->recipient)
                         ->subject($subject);
                });

                logEmailActivity(
                    $orgId,
                    null,
                    $template->title ?? 'OTP',
                    $request->recipient,
                    $subject,
                    $body,
                    'sent',
                    'Email sent successfully',
                    'smtp',
                    null
                );

            }catch(\Exception $e){

                logEmailActivity(
                    $orgId,
                    null,
                    $template->title ?? 'OTP',
                    $request->recipient,
                    $subject,
                    $body,
                    'failed',
                    $e->getMessage(),
                    'smtp',
                    null
                );

                return response()->json([
                    'status'=>false,
                    'message'=>'Email sending failed'
                ], 500);
            }
        }

        return response()->json([
            'status'=>true,
            'message'=>'OTP Sent Successfully'
        ]);
    }



    // ==================================
    // VERIFY OTP
    // ==================================
    public function verify(Request $request)
    {
        $request->validate([
            'organization_id'=>'required',
            'type'=>'required|in:email,mobile',
            'recipient'=>'required',
            'otp'=>'required'
        ]);

        $otp = OtpVerification::where([
                'organization_id'=>$request->organization_id,
                'type'=>$request->type,
                'recipient'=>$request->recipient,
                'otp_code'=>$request->otp,
                'is_verified'=>0
            ])
            ->where('expires_at','>=',now())
            ->latest()
            ->first();

        if(!$otp){
            return response()->json([
                'status'=>false,
                'message'=>'Invalid or expired OTP'
            ], 400);
        }

        $otp->update([
            'is_verified'=>1
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'OTP verified successfully'
        ]);
    }
}