<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Lead;
use App\Services\LeadDistributor;
use Carbon\Carbon;

class AutoReassignInactiveLeads extends Command
{
    protected $signature = 'leads:auto-reassign-inactive';
    protected $description = 'Auto reassign leads from inactive users to active ones';

    public function handle()
    {
        $this->info("Starting auto reassign for inactive users...");

        $inactiveDays = 2; // tum apne hisab se change kar sakte ho

        // 1) Inactive users dhundo:
        $cutoff = Carbon::now()->subDays($inactiveDays);

        $inactiveUsers = User::where('is_available_for_leads', 1)  // abhi tak available the
            ->where('updated_at', '<', $cutoff)                    // kuch time se inactive
            ->get();

        if ($inactiveUsers->isEmpty()) {
            $this->info("No inactive users found.");
            return 0;
        }

        foreach ($inactiveUsers as $user) {

            $this->line("User #{$user->id} marked inactive for leads.");

            // 2) Ab ye user leads nahi lega
            $user->is_available_for_leads = 0;
            $user->save();

            // 3) Is user ki OPEN leads reassign karo
            // yaha tum apne hisaab se lead_status filter laga sakte ho (e.g. new, in-progress etc.)
            $openLeads = Lead::where('lead_assign_to', $user->id)
                ->whereIn('lead_status', ['new', 'in-progress']) // apne system ke hisab se edit karo
                ->get();

            foreach ($openLeads as $lead) {

                // pehle assign_to clear
                $lead->lead_assign_to = 0;
                $lead->save();

                // fir naya user decide + log
                LeadDistributor::assignAndLog($lead, 'auto_reassign');

                $this->line("Lead #{$lead->id} reassigned from User #{$user->id}");
            }
        }

        $this->info("Auto reassign job completed.");
        return 0;
    }
}
