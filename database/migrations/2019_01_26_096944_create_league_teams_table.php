<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeagueTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_teams', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->default(0);
            $table->unsignedBigInteger('league_id')->default(0);
            $table->unsignedBigInteger('league_division_id')->default(0);
            $table->primary(['league_id', 'team_id']);

            $table->foreign('team_id')
                ->references('id')->on('teams')
                ->onDelete('cascade');
            
            $table->foreign('league_id')
                ->references('id')->on('leagues')
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
        Schema::dropIfExists('league_teams');
    }
}
