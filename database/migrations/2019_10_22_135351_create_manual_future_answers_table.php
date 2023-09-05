<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualFutureAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_future_answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id')->index();
            $table->unsignedBigInteger('future_id')->index();
            $table->unsignedBigInteger('candidate_id')->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->unsignedInteger('vote_count')->default(0);
            $table->integer('score')->default(0);
            $table->string('standing')->default('');
            $table->float('odds')->nullable();
            $table->integer('winning_points')->default(0);
            $table->integer('losing_points')->default(0);
            $table->boolean('is_absent')->default(FALSE);
            $table->json('meta')->nullable();
            $table->json('meta_es')->nullable();
            $table->timestamps();

            $table->foreign('page_id')
                ->references('id')->on('manual_poll_pages')
                ->onDelete('cascade');

            $table->foreign('future_id')
                ->references('id')->on('manual_futures')
                ->onDelete('cascade');

            $table->foreign('candidate_id')
                ->references('id')->on('manual_candidates')
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
        Schema::dropIfExists('manual_future_answers');
    }
}
