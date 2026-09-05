<?php

namespace App\Services\Telephony\Clients\CloudTelephony;

class ClickToCall
{
    public static function normal($organizationId, $agentNumber, $studentNumber, $deskphone = null, $campid = null, $callerid = null)
    {
		if (!$agentNumber) {
			return ['ok' => false, 'message' => 'Agent number missing'];
		}

		if (!$studentNumber) {
			return ['ok' => false, 'message' => 'Student number missing'];
		}

		$deskphone = $deskphone ?: CloudTelephonyClient::defaultDeskphone($organizationId);

		if (!$deskphone) {
			return ['ok' => false, 'message' => 'Deskphone missing in IVR settings'];
		}
        $query = [
            'calling_party_a' => $agentNumber,
            'calling_party_b' => $studentNumber,
            'deskphone' => $deskphone,
            'call_from_did' => 1,
        ];

        if ($campid) $query['campid'] = $campid;
        if ($callerid) $query['callerid'] = $callerid;

        return CloudTelephonyClient::getQueryAuth($organizationId, '/api/click_to_call_v2', $query);
    }

    public static function withGroup($organizationId, $agentNumber, $studentNumber, $deskphone, $groupName, $campid = null, $callerid = null)
    {
        $query = [
            'calling_party_a' => $agentNumber,
            'calling_party_b' => $studentNumber,
            'deskphone' => $deskphone,
            'group_name' => $groupName,
            'call_from_did' => 1,
        ];

        if ($campid) $query['campid'] = $campid;
        if ($callerid) $query['callerid'] = $callerid;

        return CloudTelephonyClient::getQueryAuth($organizationId, '/api/click_to_call_v2', $query);
    }

    // Reverse Click2Call (v3)
    public static function reverseCustomerFirst($organizationId, $customerNumber, $deskphone, $groupName = null, $memberNumber = null)
    {
        $query = [
            'calling_party_a' => $customerNumber,
            'deskphone' => $deskphone,
            'call_from_did' => 1,
        ];

        if ($groupName) {
            $query['group_name'] = $groupName;
        }

        if ($memberNumber) {
            $query['calling_party_b'] = $memberNumber;
        }

        return CloudTelephonyClient::getQueryAuth($organizationId, '/api/click_to_call_v3', $query);
    }
}
