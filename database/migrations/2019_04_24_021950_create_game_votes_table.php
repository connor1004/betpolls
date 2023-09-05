<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('game_bet_type_id')->default(0)->unique();
            $table->unsignedBigInteger('game_id')->default(0);
            $table->unsignedBigInteger('bet_type_id')->default(0);
            $table->integer('win_vote_count')->default(0);
            $table->integer('loss_vote_count')->default(0);
            $table->integer('tie_vote_count')->default(0);
            $table->enum('matched_vote_case',['win', 'loss', 'tie'])->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->unique(['game_id', 'bet_type_id']);
            $table->timestamps();
            
            $table->foreign('game_bet_type_id')
                ->references('id')->on('game_bet_types')
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
        Schema::dropIfExists('game_votes');
    }
}
