<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Lead;
use App\Services\LeadDistributor;

class DistributeLeads extends Command
{
    protected $signature = 'leads:auto-assign';
    protected $description = 'Assign all unassigned leads based on distribution rules & log each assignment';

    public function handle()
    {
        $this->info("Starting lead distribution...");

        Lead::whereNull('lead_assign_to')
            ->orWhere('lead_assign_to', 0)
            ->orderBy('id')
            ->chunkById(200, function ($leads) {

                foreach ($leads as $lead) {

                    $userId = LeadDistributor::assignAndLog($lead, 'cron');

                    $this->line("Lead #{$lead->id} → User #{$userId}");
                }
            });

        $this->info("Lead distribution completed.");

        return 0;
    }
}
