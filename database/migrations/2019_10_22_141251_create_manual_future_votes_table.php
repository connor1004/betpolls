<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualFutureVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_future_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id');
            $table->unsignedBigInteger('future_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('answer_id');
            $table->integer('score')->default(0);
            $table->unsignedTinyInteger('matched')->nullable();
            $table->boolean('calculated')->default(FALSE);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('page_id')
                ->references('id')->on('manual_poll_pages')
                ->onDelete('cascade');

            $table->foreign('future_id')
                ->references('id')->on('manual_futures')
                ->onDelete('cascade');
            
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('answer_id')
                ->references('id')->on('manual_future_answers')
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
        Schema::dropIfExists('manual_future_votes');
    }
}
