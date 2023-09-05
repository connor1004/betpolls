<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualEventVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_event_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('bet_type_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_bet_type_id');
            $table->enum('vote_case', ['win', 'loss', 'tie'])->default('loss')->index();
            $table->integer('score')->default(0);
            $table->boolean('matched')->nullable();
            $table->boolean('calculated')->default(FALSE);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->foreign('page_id')
                ->references('id')->on('manual_poll_pages')
                ->onDelete('cascade');

            $table->foreign('event_id')
                ->references('id')->on('manual_events')
                ->onDelete('cascade');

            $table->foreign('bet_type_id')
                ->references('id')->on('bet_types')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('event_bet_type_id')
                ->references('id')->on('manual_event_bet_types')
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
        Schema::dropIfExists('manual_event_votes');
    }
}
