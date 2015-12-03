<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

use App\Http\Spiders as Spiders;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Spiders\CinemaSpider::updateCache();
            Spiders\CinemaKidsSpider::updateCache();
            Spiders\CinemaPremiereSpider::updateCache();
            Spiders\TheatreSpider::updateCache();
            Spiders\PricesSpider::updateCache();
        })->everyMinute();
    }
}
