<?php

namespace App\Console;

use App\Classes\Settings;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

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
        $schedule->command('open:stock')->dailyAt('01:00');
        
        $schedule->command('orders:refresh')->everyMinute()->withoutOverlapping();

        //$schedule->command('queue:work --sansdaemon --tries=3 --timeout=0')->everyMinute()->withoutOverlapping()->appendOutputTo('storage/app/queuework.txt');

        //$schedule->command('backup:run --only-db')->dailyAt('00:00');

        $schedule->command('nearos:compute')->dailyAt('02:00');

        $schedule->command('retailnearos:compute')->dailyAt('03:00');

        $schedule->command('open:supplierdbstock')->dailyAt('07:00');

        $schedule->command('run:movingstocks')->dailyAt('04:00');

        $schedule->command('sync:stock')->everyTwoHours();

        //$schedule->command('download:product-image')->withoutOverlapping()->everyMinute()->appendOutputTo('storage/app/imageDownload.txt');

        $schedule->command('uploadproduct:image')->withoutOverlapping()->everyMinute()->appendOutputTo('storage/app/imageUpload.txt');
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
