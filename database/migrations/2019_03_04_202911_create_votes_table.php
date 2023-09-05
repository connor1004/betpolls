<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('game_bet_type_id')->default(0);
            $table->unsignedBigInteger('game_id')->default(0);
            $table->unsignedBigInteger('bet_type_id')->default(0);
            $table->unsignedBigInteger('user_id')->default(0);
            $table->enum('vote_case', ['win', 'loss', 'tie'])->default('loss')->index();
            $table->integer('score')->default(0);
            $table->boolean('matched')->nullable();
            $table->boolean('calculated')->default(false);
            $table->unique(['game_bet_type_id', 'user_id']);
            $table->unique(['game_id', 'bet_type_id', 'user_id']);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

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
        Schema::dropIfExists('votes');
    }
}
