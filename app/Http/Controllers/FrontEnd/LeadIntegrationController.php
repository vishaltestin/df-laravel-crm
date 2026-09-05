<?php
 namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LeadIntegration;
use App\LeadIntegrationCredential;
use App\LeadWebhookLog;
use Illuminate\Support\Facades\Crypt;
use Sentinel;

class LeadIntegrationController extends Controller
{
    public function facebook()
    {
        return $this->integrationView('facebook');
    }

    public function google()
    {
        return $this->integrationView('google');
    }

    public function instagram()
    {
        return $this->integrationView('instagram');
    }

    /**
     * Load integration + SAFE decrypted credentials
     */
    private function integrationView(string $type)
    {
        $user = Sentinel::check();
        $organizationId = registeruser($user->id)->organization;

        $integration = LeadIntegration::where([
            'organization_id' => $organizationId,
            'provider'        => $type,
        ])->first();

        $rawCredentials = LeadIntegrationCredential::where([
            'organization_id' => $organizationId,
            'provider'        => $type,
        ])->get();

        /**
         * 🔐 SAFE decrypt (no crash)
         */
        $credentials = collect();

        foreach ($rawCredentials as $cred) {
            $value = '';

            if (!empty($cred->value)) {
                try {
                    // encrypted value
                    $value = Crypt::decryptString($cred->value);
                } catch (\Exception $e) {
                    // plain / broken value fallback
                    $value = $cred->value;
                }
            }

            $credentials->put($cred->key, (object)[
                'key'   => $cred->key,
                'value' => $value
            ]);
        }

        return view("FrontEnd.lead_integrations.$type", compact(
            'integration',
            'credentials'
        ));
    }

    /**
     * 🔥 ONE SAVE METHOD FOR ALL INTEGRATIONS
     */
    public function save(Request $request)
    {
        $user = Sentinel::check();
        $organizationId = registeruser($user->id)->organization;

        foreach ($request->integrations as $type => $data) {

            // ---------- integration toggle ----------
            LeadIntegration::updateOrCreate(
                [
                    'organization_id' => $organizationId,
                    'provider'        => $type,
                ],
                [
                    'is_enabled' => isset($data['enabled']) ? 1 : 0,
                ]
            );

            // ---------- credentials ----------
            if (!empty($data['credentials'])) {
                foreach ($data['credentials'] as $key => $value) {

                    if ($value === null || $value === '') {
                        continue;
                    }

                    LeadIntegrationCredential::updateOrCreate(
                        [
                            'organization_id' => $organizationId,
                            'provider'        => $type,
                            'key'             => $key,
                        ],
                        [
                            // 🔐 ALWAYS encrypt on save
                            'value' => Crypt::encryptString($value),
                        ]
                    );
                }
            }
        }

        return back()->with('success', 'Integration settings saved successfully');
    }

    public function logs()
    {
        $logs = LeadWebhookLog::latest()->paginate(50);
return view('FrontEnd.lead_integrations.logs', compact('logs'));
    }
}
