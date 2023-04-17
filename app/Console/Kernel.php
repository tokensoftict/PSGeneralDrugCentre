<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('open:stock')->dailyAt('01:00')->sendOutputTo('storage/app/stockopening.txt');;

        $schedule->command('orders:refresh')->everyMinute()->withoutOverlapping()->sendOutputTo('storage/app/orderrefresh.txt');;

        $schedule->command('queue:work --sansdaemon --tries=3 --timeout=0')->everyMinute()->withoutOverlapping()->sendOutputTo('storage/app/queuework.txt');;

        $schedule->command('backup:run --only-db')->dailyAt('00:00')->sendOutputTo('storage/app/backup.txt');;

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
