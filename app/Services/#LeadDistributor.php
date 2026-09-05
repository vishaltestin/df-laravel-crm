<?php

namespace App\Services;

use App\Lead;
use App\Register;
use App\LeadDistributionRule;
use App\LeadDistributionRuleItem;
use App\LeadAssignmentLog;

class LeadDistributor
{
    /**
     * Ye method:
     *  - rule decide karega
     *  - lead_assign_to update karega
     *  - log table me entry dalega
     *  - aur final user_id return karega
     */
    public static function assignAndLog(Lead $lead, string $context = 'cron'): int
    {
        $orgId = $lead->organization_id;
        $previous = $lead->lead_assign_to ?: null;

        // --- Rule decide karo (source → random → fallback)
        [$userId, $ruleType, $ruleId] = self::decideUser($lead, $orgId);

        // --- Lead update
        $lead->lead_assign_to = $userId;
        $lead->save();

        // --- Log entry
        LeadAssignmentLog::create([
            'lead_id'              => $lead->id,
            'organization_id'      => $orgId,
            'lead_source_id'       => $lead->lead_source_id,
            'assigned_to'          => $userId,
            'previous_assigned_to' => $previous,
            'rule_type'            => $ruleType,         // source / random / fallback / auto_reassign (future)
            'rule_id'              => $ruleId,
            'is_reassign'          => $previous ? 1 : 0, // future ke auto-reassign ke liye kaam aayega
            'notes'                => $context,          // 'cron', 'manual', 'import' etc.
        ]);

        return $userId;
    }


    /**
     * Ye sirf decide karega ki kisko assign karna hai
     * Return: [user_id, rule_type, rule_id]
     */
    protected static function decideUser(Lead $lead, int $orgId): array
    {
        // 1) Source based
        $fromSource = self::assignBySource($lead, $orgId);
        if ($fromSource) {
            return [$fromSource['user_id'], 'source', $fromSource['rule_id']];
        }

        // 2) Random rule
        $fromRandom = self::assignRandomRule($lead, $orgId);
        if ($fromRandom) {
            return [$fromRandom['user_id'], 'random', $fromRandom['rule_id']];
        }

        // 3) Fallback → Admin id=1
        //return [1, 'fallback', null];
		 

return [null, 'no_rule', null];
    }


    /**
     * SOURCE BASED ASSIGNMENT
     * return: ['user_id'=>X, 'rule_id'=>Y] or null
     */
    protected static function assignBySource(Lead $lead, int $orgId): ?array
    {
        if (!$lead->lead_source_id) {
            return null;
        }

        $items = LeadDistributionRuleItem::where('source_id', $lead->lead_source_id)
            ->whereHas('rule', function ($q) use ($orgId) {
                $q->where('organization_id', $orgId)
                  ->where('is_active', 1)
                  ->where('distribution_type', 'source');
            })
            ->with('rule')
            ->get();

        if ($items->isEmpty()) {
            return null;
        }

        $userPool = [];
        $ruleId = null;

        foreach ($items as $item) {

            // first item ka rule_id le lo (almost hamesha same rahega)
            if (!$ruleId && $item->rule) {
                $ruleId = $item->rule->id;
            }

            // Direct user
            if ($item->assign_type === 'user' && $item->user_id) {
                $userPool[] = [
                    'user_id' => $item->user_id,
                    'weight'  => $item->percentage ?? 1,
                ];
            }

            // Team based
            if ($item->assign_type === 'team' && $item->team_id) {
               $teamUsers = Register::where('team', $item->team_id)
				->where('organization', $orgId)
				->get();

                foreach ($teamUsers as $u) {
                    $userPool[] = [
                        'user_id' => $u->id,
                        'weight'  => $item->percentage ?? 1,
                    ];
                }
            }

            // Designation based
            if ($item->assign_type === 'designation' && $item->designation_id) {
                $desUsers = Register::where('designation_id', $item->designation_id)
				->where('organization', $orgId)
				->get();

                foreach ($desUsers as $u) {
                    $userPool[] = [
                        'user_id' => $u->id,
                        'weight'  => $item->percentage ?? 1,
                    ];
                }
            }
        }

        if (empty($userPool)) {
            return null;
        }

        // Kya kisi ke paas percentage set hai?
        $hasPercent = collect($userPool)->pluck('weight')->filter()->count() > 0;

        if ($hasPercent) {
            $userId = self::weightedPick($userPool);
        } else {
            $userId = collect($userPool)->random()['user_id'];
        }

        return [
            'user_id' => $userId,
            'rule_id' => $ruleId,
        ];
    }


    /**
     * Weighted random pick
     */
    protected static function weightedPick(array $pool): int
    {
        $weighted = [];

        foreach ($pool as $p) {
            $w = max(1, (int)($p['weight'] ?: 1));
            for ($i = 0; $i < $w; $i++) {
                $weighted[] = $p['user_id'];
            }
        }

        return $weighted[array_rand($weighted)];
    }


    /**
     * RANDOM RULE (round robin)
     * return: ['user_id'=>X, 'rule_id'=>Y] or null
     */
    protected static function assignRandomRule(Lead $lead, int $orgId): ?array
    {
        $rule = LeadDistributionRule::where('organization_id', $orgId)
            ->where('distribution_type', 'random')
            ->where('is_active', 1)
            ->with('items')
            ->first();

        if (!$rule) {
            return null;
        }

        $userIds = $rule->items->pluck('user_id')->filter()->toArray();

        if (empty($userIds)) {
            return null;
        }

        // sirf iss org ke users
        $userIds = Register::whereIn('id', $userIds)
		->where('organization', $orgId)
		->pluck('id')
		->toArray();

        if (empty($userIds)) {
            return null;
        }

        // last assigned user among these
        $lastUserId = Lead::whereIn('lead_assign_to', $userIds)
            ->orderBy('id', 'desc')
            ->value('lead_assign_to');

        if ($lastUserId) {
            $pos = array_search($lastUserId, $userIds);
            $nextIndex = ($pos === false || $pos == count($userIds) - 1) ? 0 : $pos + 1;
            $userId = $userIds[$nextIndex];
        } else {
            // pehli baar
            $userId = $userIds[array_rand($userIds)];
        }

        return [
            'user_id' => $userId,
            'rule_id' => $rule->id,
        ];
    }
}
