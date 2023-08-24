<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
