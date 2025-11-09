<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\RunTraceCommand::class,
        \App\Console\Commands\TraceDiagnosticsCommand::class, // ✅ ADD THIS LINE
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('shopify:sync-products')->everyThreeHours();
        $schedule->command('shopify:sync-orders')->everyThreeHours();
        $schedule->command('trace:diagnostics')->dailyAt('00:00');
        // ✅ Optional: run diagnostics daily if you want
        // $schedule->command('trace:diagnostics')->dailyAt('3:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}