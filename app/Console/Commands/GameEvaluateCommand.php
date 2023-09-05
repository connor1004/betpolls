<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Leaderboard;
use App\Facades\Evaluation;

/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class GameEvaluateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "game:evaluate {period_type?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Evaluate the game";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $period_type = $this->argument('period_type');
        if (empty($period_type)) {
            $period_type = Leaderboard::$PERIOD_TYPE_WEEKLY;
        }
        Evaluation::evaluateUser($period_type);
    }
}
