<?php
// database/seeders/LeadStatusSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LeadStatus;
use App\Models\LeadSubStatus;

class LeadStatusSeeder extends Seeder
{
    public function run()
    {
        // GLOBAL default (organization_id = null)

        $cold = LeadStatus::create(['name' => 'Cold', 'sort_order' => 1]);
        $warm = LeadStatus::create(['name' => 'Warm', 'sort_order' => 2]);
        $hot  = LeadStatus::create(['name' => 'Hot',  'sort_order' => 3]);
        $submitted = LeadStatus::create(['name' => 'Submitted', 'sort_order' => 4]);
        $closed    = LeadStatus::create(['name' => 'Closed', 'sort_order' => 5]);
        $notReachable = LeadStatus::create(['name' => 'Not Reachable', 'sort_order' => 6]);
        $nextSession  = LeadStatus::create(['name' => 'Next Session', 'sort_order' => 7]);
        $junk = LeadStatus::create(['name' => 'Junk or Test lead', 'sort_order' => 8]);

        // Cold
        LeadSubStatus::insert([
            ['lead_status_id' => $cold->id, 'name' => 'Asked To Call Back', 'sort_order' => 1],
            ['lead_status_id' => $cold->id, 'name' => 'NOT INTERESTED', 'sort_order' => 2],
            ['lead_status_id' => $cold->id, 'name' => 'Disconnected', 'sort_order' => 3],
        ]);

        // Warm
        LeadSubStatus::insert([
            ['lead_status_id' => $warm->id, 'name' => 'Asked To Call Back', 'sort_order' => 1],
            ['lead_status_id' => $warm->id, 'name' => 'Need to discuss', 'sort_order' => 2],
            ['lead_status_id' => $warm->id, 'name' => 'Busy/Call Waiting', 'sort_order' => 3],
        ]);

        // Hot
        LeadSubStatus::insert([
            ['lead_status_id' => $hot->id, 'name' => 'Asked To Call Back', 'sort_order' => 1],
            ['lead_status_id' => $hot->id, 'name' => 'Will Submit Soon', 'sort_order' => 2],
            ['lead_status_id' => $hot->id, 'name' => 'Interested in filling form', 'sort_order' => 3],
        ]);

        // Submitted
        LeadSubStatus::insert([
            ['lead_status_id' => $submitted->id, 'name' => 'Application Submitted', 'sort_order' => 1],
            ['lead_status_id' => $submitted->id, 'name' => 'Admission Taken', 'sort_order' => 2],
            ['lead_status_id' => $submitted->id, 'name' => 'Kit Processed', 'sort_order' => 3],
        ]);

        // Closed
        LeadSubStatus::insert([
            ['lead_status_id' => $closed->id, 'name' => 'Invalid Email', 'sort_order' => 1],
            ['lead_status_id' => $closed->id, 'name' => 'Not interested', 'sort_order' => 2],
            ['lead_status_id' => $closed->id, 'name' => 'Wrong Number', 'sort_order' => 3],
            ['lead_status_id' => $closed->id, 'name' => 'Taken Admission Elsewhere', 'sort_order' => 4],
            ['lead_status_id' => $closed->id, 'name' => 'Invalid Number', 'sort_order' => 5],
            ['lead_status_id' => $closed->id, 'name' => 'Admission/Application done with another lead', 'sort_order' => 6],
            ['lead_status_id' => $closed->id, 'name' => 'Service Unreachable', 'sort_order' => 7],
        ]);

        // Not Reachable
        LeadSubStatus::insert([
            ['lead_status_id' => $notReachable->id, 'name' => 'Number Busy', 'sort_order' => 1],
            ['lead_status_id' => $notReachable->id, 'name' => 'Not Picking', 'sort_order' => 2],
            ['lead_status_id' => $notReachable->id, 'name' => 'Number Switched Off', 'sort_order' => 3],
        ]);

        // Next Session – फिलहाल खाली रहने दिये; user खुद add करेगा
    }
}
