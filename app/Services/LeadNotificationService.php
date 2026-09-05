<?php

namespace App\Services;

use App\Lead;
use App\User;

class LeadNotificationService
{
    /**
     * Jab bhi lead assign/reassign ho, isko call karein
     */
    public static function notifyOnAssignment(Lead $lead, User $user, string $ruleType, string $context)
    {
        // 1) Customer notification (SMS / Email / WhatsApp)
        //  -> yaha tum apna SMS/WhatsApp/Email integration call kar sakte ho
        //  Example: SmsService::sendLeadAssignedCustomer($lead, $user);

        // 2) User notification (Agent)
        //  Example: SmsService::sendLeadAssignedToUser($lead, $user);

        // 3) Admin notification (optional)
        //  Example: NotificationService::notifyAdminNewLeadAssignment($lead, $user, $ruleType, $context);

        // Abhi filhal methods blank rakhe hain, tum apne existing template system se integrate kar lena.
    }
}
