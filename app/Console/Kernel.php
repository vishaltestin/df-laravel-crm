<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
   protected function schedule(Schedule $schedule): void
	{
		$schedule->command('leads:auto-assign')->everyMinute();

		$schedule->command('leads:auto-reassign-inactive')->everyMinute();

		$schedule->command('raw-leads:process --limit=1')->everyMinute();

		$schedule->command('crm:send-pending-sms --limit=25')->everyMinute();

		$schedule->command('crm:send-pending-email --limit=10')->everyMinute();

		$schedule->command('crm:send-pending-whatsapp --limit=20')->everyMinute();

		// Lead uploads job dispatch
		$schedule->call(function () {
			$uploads = \DB::table('lead_uploads')
				->where('status', 'uploaded')
				->limit(5)
				->get();

			foreach ($uploads as $upload) {
				dispatch(new \App\Jobs\ProcessLeadUploadJob($upload->id));
			}
		})->everyMinute();
	}
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}

