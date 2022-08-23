<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Facades\App\Http\Controllers\Ajax\BigQueryController as BigQuery;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\MakeProcessorCommand::class,
        \App\Console\Commands\MakeBundleView::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->call(function () {
        //     BigQuery::fetchToLocal();
        // })
        //     ->dailyAt('00:10');

        $schedule->command('notify:issues')
                ->daily()
                ->dailyAt('17:00')
                ->weekdays();
        
        $schedule->command('update:name')
                ->daily()
                ->dailyAt('00:00')
                ->weekdays();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
