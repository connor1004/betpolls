<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameBetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_bet_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('game_id');
            $table->unsignedBigInteger('bet_type_id');
            $table->float('weight_1')->default(0);
            $table->float('weight_2')->default(0);
            $table->float('weight_3')->default(0);
            $table->float('weight_4')->default(0);
            $table->float('weight_5')->default(0);
            $table->timestamps();
            $table->unique(['game_id', 'bet_type_id']);

            $table->foreign('game_id')
                ->references('id')->on('games')
                ->onDelete('cascade');
            
            $table->foreign('bet_type_id')
                ->references('id')->on('bet_types')
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
        Schema::dropIfExists('game_bet_types');
    }
}
