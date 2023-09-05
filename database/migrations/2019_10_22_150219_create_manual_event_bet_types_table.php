<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualEventBetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_event_bet_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id');
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('bet_type_id');
            $table->unsignedInteger('win_vote_count')->default(0);
            $table->unsignedInteger('loss_vote_count')->default(0);
            $table->unsignedInteger('tie_vote_count')->default(0);
            $table->enum('matched_vote_case',['win', 'loss', 'tie'])->nullable();
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manual_event_bet_types');
    }
}
