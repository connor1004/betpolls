<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ref_id')->default(0);
            $table->unsignedBigInteger('league_id');
            $table->dateTime('start_at');
            
            $table->unsignedBigInteger('home_team_id');
            $table->integer('home_team_score')->default(0);

            $table->unsignedBigInteger('away_team_id');
            $table->integer('away_team_score')->default(0);
            
            $table->enum('status', ['not_started', 'postponed', 'started', 'ended'])->default('not_started')->index();

            $table->integer('voter_count')->default(0);
            $table->integer('vote_count')->default(0);

            $table->json('meta')->nullable();
            $table->json('meta_es')->nullable();
            $table->json('game_info')->nullable();
            $table->json('game_general_info')->nullable();

            $table->boolean('calculated')->default(false);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('calculating_at')->nullable();

            $table->unique(['ref_id', 'league_id', 'start_at', 'home_team_id', 'away_team_id']);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('league_id')
                ->references('id')->on('leagues')
                ->onDelete('cascade');

            $table->foreign('home_team_id')
                ->references('id')->on('teams')
                ->onDelete('cascade');

            $table->foreign('away_team_id')
                ->references('id')->on('teams')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
