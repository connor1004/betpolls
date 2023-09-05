<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeagueBetTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_bet_type', function (Blueprint $table) {
            $table->unsignedBigInteger('league_id')->index();
            $table->unsignedBigInteger('bet_type_id')->index();
            $table->timestamps();
            $table->primary(['league_id', 'bet_type_id']);

            $table->foreign('league_id')
                ->references('id')->on('leagues')
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
        Schema::dropIfExists('league_bet_type');
    }
}
