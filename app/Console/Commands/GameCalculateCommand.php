<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Facades\Calculation;

/**
 * Class deletePostsCommand
 *
 * @category Console_Command
 * @package  App\Console\Commands
 */
class GameCalculateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "game:calculate {game?}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Calculate the game";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $game_id = $this->argument('game');
        Calculation::calculateGames($game_id);
    }
}
