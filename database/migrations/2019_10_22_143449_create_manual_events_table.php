<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id');
            $table->string('name');
            $table->string('name_es');
            $table->unsignedInteger('display_order')->default(0);
            $table->unsignedBigInteger('candidate1_id');
            $table->integer('candidate1_score')->default(0);
            $table->string('candidate1_standing')->default('');
            $table->float('candidate1_odds')->default(0);
            $table->unsignedBigInteger('candidate2_id');
            $table->integer('candidate2_score')->default(0);
            $table->string('candidate2_standing')->default('');
            $table->float('candidate2_odds')->default(0);
            $table->float('spread')->default(0);
            $table->float('over_under')->default(0);
            $table->float('over_under_score')->default(0);
            $table->unsignedInteger('voter_count')->default(0);
            $table->unsignedInteger('vote_count')->default(0);
            $table->json('meta')->nullable();
            $table->json('meta_es')->nullable();
            $table->boolean('calculated')->default(FALSE);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('calculating_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('page_id')
                ->references('id')->on('manual_poll_pages')
                ->onDelete('cascade');

            $table->foreign('candidate1_id')
                ->references('id')->on('manual_candidates')
                ->onDelete('cascade');

            $table->foreign('candidate2_id')
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
        Schema::dropIfExists('manual_events');
    }
}
