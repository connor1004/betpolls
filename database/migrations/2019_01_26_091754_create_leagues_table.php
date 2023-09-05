<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leagues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('logo')->nullable();
            $table->unsignedBigInteger('sport_category_id')->default(0)->index();
            $table->unsignedBigInteger('sport_area_id')->default(0)->index();
            $table->unsignedInteger('display_order')->default(0);
            $table->string('name');
            $table->string('name_es');
            $table->string('slug')->default('');
            $table->string('slug_es')->default('');
            $table->string('title')->default('');
            $table->string('title_es')->default('');
            $table->string('meta_keywords')->default('');
            $table->string('meta_keywords_es')->default('');
            $table->string('meta_description', 512)->default('');
            $table->string('meta_description_es', 512)->default('');
            $table->text('content')->nullable();
            $table->text('content_es')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leagues');
    }
}
