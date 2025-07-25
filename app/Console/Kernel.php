<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Modules\Starter\Entities\MessageBatch;
use Modules\Starter\Jobs\DispatchBatchMessageJob;

class Kernel extends ConsoleKernel
{
	/**
	 * Define the application's command schedule.
	 */
	protected function schedule(Schedule $schedule): void
	{
		//为了避免数据过多，定期清理
		$schedule->command('telescope:prune --hours=168')->daily(); //每周清理 telescope 数据
		$schedule->command('queue:prune-batches --hours=168')->daily(); //每周清理 queue batch 数据
		$schedule->call(function () {
			MessageBatch::where('send_type', 'cron')
				->where('is_active', true)
				->get()
				->each(function ($batch) {
					$cron = $batch->send_params['cron'] ?? null;
					if (land_cron_matches_now($cron, "-")) {
						DispatchBatchMessageJob::dispatch($batch->id);
					}
				});
		})->everyMinute();
	}

	/**
	 * Register the commands for the application.
	 */
	protected function commands(): void
	{
		$this->load(__DIR__ . '/Commands');

		require base_path('routes/console.php');
	}
}
