<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualPollPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_poll_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('subcategory_id')->default(0);
            $table->dateTime('start_at');
            $table->string('name');
            $table->string('name_es');
            $table->string('location');
            $table->string('location_es');
            $table->string('logo')->nullable();
            $table->boolean('show_scores')->default(FALSE);
            $table->boolean('home_top_picks')->default(FALSE);
            
            $table->enum('status', ['not_started', 'postponed', 'started', 'ended'])->default('not_started')->index();
            $table->boolean('is_future')->default(FALSE);
            $table->json('meta')->nullable();
            $table->json('meta_es')->nullable();

            $table->boolean('calculated')->default(false);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('calculating_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')
                ->references('id')->on('manual_categories')
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
        Schema::dropIfExists('manual_poll_pages');
    }
}
