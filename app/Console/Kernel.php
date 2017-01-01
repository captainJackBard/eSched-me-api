<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Activity;
use App\Module;

use DateTime;

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
        $schedule->call(function() {
            // TODO : This cron job will run everyminute, check if the project is 
            // and change its status to 'failed'
            $projects = Activity::where('end', '<=', new DateTime())->where('status', 'ongoing');
            $projects->update(["status" => "failed"]);
            // TODO : Optionally broadcast an event here that a project has failed once we
            // implemented the laravel events and socket.io or pusher

            $modules = Module::where('end', '<=', new DateTime())->where('status', 'ongoing');
            $modules->update(["status" => "failed"]);
        });
    }
}
