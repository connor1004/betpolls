<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\Calculation;

use App\Game;

/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class GameUpdateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "game:update {period_type?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update the game";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period_type = $this->argument('period_type');
        if (empty($period_type)) {
            $period_type = 'day';
        }
        Calculation::updateGames($period_type);
    }
}
