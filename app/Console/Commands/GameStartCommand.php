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
class GameStartCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "game:start";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Start the game";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Calculation::startGames();
    }
}
