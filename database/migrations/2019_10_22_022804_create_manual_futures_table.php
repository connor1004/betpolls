<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualFuturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_futures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('page_id')->index();

            $table->string('name');
            $table->string('name_es');

            $table->unsignedInteger('voter_count')->default(0);

            $table->unsignedInteger('display_order')->default(0);

            $table->json('meta')->nullable();
            $table->json('meta_es')->nullable();

            $table->boolean('calculated')->default(false);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamp('calculating_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('page_id')
                ->references('id')->on('manual_poll_pages')
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
        Schema::dropIfExists('manual_futures');
    }
}
