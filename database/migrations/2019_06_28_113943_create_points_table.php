<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sport_category_id')->default(0);
            $table->unsignedBigInteger('league_id')->default(0);
            $table->unsignedBigInteger('position')->nullable()->index();
            $table->unsignedBigInteger('score')->default(0);
            $table->date('start_at');
            $table->timestamps();
            $table->unique(['user_id', 'sport_category_id', 'league_id', 'start_at'], 'SECONDARY');

            $table->foreign('user_id')
                ->references('id')->on('users')
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
        Schema::dropIfExists('points');
    }
}
