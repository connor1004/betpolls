<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GameStartCommand::class,
        Commands\GameCalculateCommand::class,
        Commands\GameEvaluateCommand::class,
        Commands\GameUpdateCommand::class,
        Commands\GameAutoVoteCommand::class,
        Commands\ClearVoteCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('game:start')->everyFiveMinutes();
        $schedule->command('game:update day')->everyMinute()->when(function() {
            $now = new \DateTime();
            if ($now->format('i') % 2 === 1) {
                return false;
            }
            return true;
        });
        $schedule->command('game:update month')->hourly();
        $schedule->command('game:calculate')->everyTenMinutes();
        $schedule->command('game:evaluate weekly')->weeklyOn(1, '8:00');
        $schedule->command('game:evaluate monthly')->monthlyOn(1, '8:00');
        // $schedule->command('game:evaluate yearly')->yearly();
        $schedule->command('game:evaluate yearly')->cron('0 8 1 1 *');
        $schedule->command('game:autovote')->hourly();

    }
}
