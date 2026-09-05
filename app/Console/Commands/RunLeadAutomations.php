<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lead;
use App\Services\AutomationRuleEngine;

class RunLeadAutomations extends Command
{
    protected $signature = 'automation:run-leads';
    protected $description = 'Run automation rules for inactive/followup missed leads';

    public function handle()
    {
        // Example: inactive = 7 days no update
        $leads = Lead::where('updated_at', '<', now()->subDays(7))
            ->limit(200)
            ->get();

        foreach ($leads as $lead) {
            AutomationRuleEngine::run('lead.inactive', $lead->id, $lead->institute_id);
        }

        $this->info("Done");
    }
}
