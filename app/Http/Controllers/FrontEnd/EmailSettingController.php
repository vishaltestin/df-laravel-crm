<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\EmailSetting;
use Illuminate\Http\Request;

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class EmailSettingController extends Controller
{
    // ===============================
    // 🔹 Helper Functions
    // ===============================
    private function actor()
    {
        return \Sentinel::check();
    }

    private function actorRole()
    {
        $u = $this->actor();
        return $u ? $u->roles()->first()->slug : null;
    }

    private function actorOrg()
    {
        $u = $this->actor();
        $reg = registeruser($u->id);
        return $reg->organization ?? null;
    }

    // ===============================
    // 1️⃣ INDEX (View + Auto Create Setting)
    // ===============================
    public function index()
    {
        $user   = $this->actor();
        $role   = $this->actorRole();
        $orgId  = $this->actorOrg();

        // root → किसी भी organization का setting override कर सकता है
        if ($role === 'root') {
            $setting = EmailSetting::firstOrCreate(['organization_id' => $orgId]);
        }
        // admin + sadmin → अपनी organization ही manage कर सकते हैं
        elseif (in_array($role, ['admin','sadmin'])) {
            $setting = EmailSetting::firstOrCreate(['organization_id' => $orgId]);
        }
        else {
            // manager / operater → केवल देख सकते हैं
            $setting = EmailSetting::where('organization_id', $orgId)->first();
        }

        return view('FrontEnd.email.form', compact('setting', 'orgId', 'role'));
    }

    // ===============================
    // 2️⃣ UPDATE EMAIL SETTINGS
    // ===============================
    public function update(Request $request)
    {
        $user   = $this->actor();
        $role   = $this->actorRole();
        $orgId  = $this->actorOrg();

        // केवल root / admin / sadmin update कर सकते हैं
        if (!in_array($role, ['root','admin','sadmin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not allowed to update Email settings!'
            ]);
        }

        $setting = EmailSetting::where('organization_id', $orgId)->first();

        if (!$setting)
            return response()->json(['status'=>'error','message'=>'No settings found!']);

        $old = $setting->toArray();

        $setting->update($request->all());

        logUserActivity($orgId, $user->id, 'email_setting', $setting->id, 'update', 'Updated Email Settings');
        logOrganizationActivity($orgId, $user->id, 'email_setting', $setting->id, 'update', 'Updated Email Settings');

        return response()->json([
            'status' => 'success',
            'message' => 'Email settings updated successfully!'
        ]);
    }

    // ===============================
    // 3️⃣ DELETE (Restricted)
    // ===============================
    public function destroy($id)
    {
        $user  = $this->actor();
        $role  = $this->actorRole();
        $orgId = $this->actorOrg();

        $setting = EmailSetting::findOrFail($id);

        // delete सिर्फ root कर सकता है
        if ($role !== 'root') {
            return response()->json(['status'=>'error','message'=>'You are not allowed to delete this!']);
        }

        $old = $setting->toArray();

        $setting->delete();

        logUserActivity($orgId, $user->id, 'email_setting', $id, 'delete', 'Deleted Email Settings');
        logOrganizationActivity($orgId, $user->id, 'email_setting', $id, 'delete', 'Deleted Email Settings');

        return response()->json(['status'=>'success','message'=>'Deleted!']);
    }

    // ===============================
    // 4️⃣ TEST SMTP (Fully Fixed)
    // ===============================
   public function testMail()
{
    $user   = $this->actor();
    $orgId  = $this->actorOrg();

    $smtp = EmailSetting::where('organization_id', $orgId)->first();

    if (!$smtp) {
        return "<h3 style='color:red;'>No SMTP settings found.</h3>";
    }

    $fromEmail = $smtp->mail_from_address ?: $smtp->mail_username;
    $fromName  = $smtp->mail_from_name ?: "CRM System";

    if (!$fromEmail) {
        return "<h3 style='color:red;'>FROM Email not configured!</h3>";
    }

    $toEmail = registeruser($user->id)->email;

    // SAFE DSN
    $dsn = sprintf(
        "smtp://%s:%s@%s:%s?encryption=%s",
        urlencode($smtp->mail_username),
        urlencode($smtp->mail_password),
        $smtp->mail_host,
        $smtp->mail_port,
        $smtp->mail_encryption ?: 'tls'
    );

    // -----------------------------------
    // Attempt Sending
    // -----------------------------------

    $subject = "SMTP Test - Connected Successfully";
    $bodyText = "Your SMTP settings are working!";

    try {
        $transport = Transport::fromDsn($dsn);

        $email = (new Email())
            ->from(new Address($fromEmail, $fromName))
            ->to($toEmail)
            ->subject($subject)
            ->text($bodyText);

        $transport->send($email);

        // 🔥 SUCCESS LOG
        logEmailActivity(
            $organization_id = $orgId,
            $user_id         = $user->id,
            $template_name   = "SMTP Test",
            $to_email        = $toEmail,
            $subject         = $subject,
            $body            = $bodyText,
            $status          = "success",
            $response_message= "Mail delivered successfully",
            $provider        = $smtp->mail_host,
            $sent_by         = $user->id
        );

        return "<h2 style='color:green;'>SMTP Test Successful! Check your inbox.</h2>";
    }
    catch (TransportException $e) {

        // 🔥 FAILURE LOG
        logEmailActivity(
            $organization_id = $orgId,
            $user_id         = $user->id,
            $template_name   = "SMTP Test",
            $to_email        = $toEmail,
            $subject         = $subject,
            $body            = $bodyText,
            $status          = "failed",
            $response_message= $e->getMessage(),
            $provider        = $smtp->mail_host,
            $sent_by         = $user->id
        );

        return "
            <h3 style='color:red;'>SMTP FAILED!</h3>
            <pre>
".$e->getMessage()."
=====================
🔥 HOW TO FIX GMAIL SMTP (Step by Step)
=====================
1) Enable 2FA  
2) Create App Password  
3) Use app password for mail_password  
4) Host: smtp.gmail.com  
5) Port: 587 (TLS)  
</pre>";
    }
}

}
