<?php

namespace App\Services;

use App\AutomationRule;
use App\AutomationRuleLog;
use App\Lead;
use Illuminate\Support\Facades\DB;

class AutomationRuleEngine
{
    public static function run(string $event, int $leadId, int $orgId)
    {
        $lead = Lead::where('id', $leadId)
            ->where('institute_id', $orgId) // tumhare schema me org = institute_id
            ->first();

        if (!$lead) return;

        $rules = AutomationRule::where('organization_id', $orgId)
            ->where('trigger_event', $event)
            ->where('is_active', 1)
            ->get();

        foreach ($rules as $rule) {

            // 🔥 Duplicate spam रोकने के लिए
            $alreadyRan = AutomationRuleLog::where('rule_id', $rule->id)
                ->where('lead_id', $leadId)
                ->where('trigger_event', $event)
                ->exists();

            if ($alreadyRan) {
                continue;
            }

            // Conditions match?
            $match = self::matchConditions($lead, $rule->conditions ?? []);

            if (!$match) {
                AutomationRuleLog::create([
                    'org_id' => $orgId,
                    'rule_id' => $rule->id,
                    'lead_id' => $leadId,
                    'trigger_event' => $event,
					'action_type'   => $rule->action_type ?? 'skipped',  // ✅ FIX
					'status' => 'skipped',
                    'message' => 'Conditions not matched'
                ]);
                continue;
            }

            // Execute action
            try {
                DB::beginTransaction();

                self::executeAction($lead, $rule);

                AutomationRuleLog::create([
                    'org_id' => $orgId,
                    'rule_id' => $rule->id,
                    'lead_id' => $leadId,
                    'trigger_event' => $event,
                    'action_type' => $rule->action_type  ?? 'success',  // ✅ FIX

                    'status' => 'success',
                    'message' => 'Executed successfully'
                ]);

                DB::commit();
            } catch (\Throwable $e) {

                DB::rollBack();

                AutomationRuleLog::create([
                    'org_id' => $orgId,
                    'rule_id' => $rule->id,
                    'lead_id' => $leadId,
                    'trigger_event' => $event,
                    'action_type' => $rule->action_type ?? 'failed',
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

    /* ============================
       CONDITIONS MATCHING
    ============================ */
    private static function matchConditions($lead, array $conditions): bool
    {
        if (empty($conditions)) return true;

        foreach ($conditions as $c) {

            $field = $c['field'] ?? null;
            $op    = $c['operator'] ?? '=';
            $value = $c['value'] ?? null;

            if (!$field) continue;

            $leadValue = self::getLeadFieldValue($lead, $field);

            // Null safe
            $leadValueStr = strtolower((string) $leadValue);
            $valueStr     = strtolower((string) $value);

            if ($op === '=') {
                if ((string)$leadValue !== (string)$value) return false;
            }

            if ($op === '!=') {
                if ((string)$leadValue === (string)$value) return false;
            }

            if ($op === 'contains') {
                if (!str_contains($leadValueStr, $valueStr)) return false;
            }
        }

        return true;
    }

    private static function getLeadFieldValue($lead, string $field)
    {
        // Tumhare UI fields mapping:
        // source, campaign, status, course, state, city

        return match ($field) {
            'source'   => $lead->source_id ?? null,
            'campaign' => $lead->campaign_id ?? null,
            'status'   => $lead->status ?? null,
            'course'   => $lead->programme_id ?? null,
            'state'    => $lead->state_id ?? null,
            'city'     => $lead->city_id ?? null,
            default    => null
        };
    }

    /* ============================
       ACTION EXECUTION
    ============================ */
    private static function executeAction($lead, AutomationRule $rule)
    {
        $type = $rule->action_type;
        $data = $rule->action ?? [];

        if ($type === 'assign_team') {
            $teamId = $data['team_id'] ?? null;
            if (!$teamId) return;

            $lead->team_id = $teamId;
            $lead->save();
            return;
        }

        if ($type === 'assign_user') {
            $userId = $data['user_id'] ?? null;
            if (!$userId) return;

            $lead->assigned_to = $userId;
            $lead->save();
            return;
        }

        // Communication placeholders
        if ($type === 'send_email') {
            // template_id = $data['template_id']
            // TODO: integrate email sending
            return;
        }

        if ($type === 'send_sms') {
            // TODO: integrate SMS gateway
            return;
        }

        if ($type === 'send_whatsapp') {
            // TODO: integrate WhatsApp API
            return;
        }
    }
}
