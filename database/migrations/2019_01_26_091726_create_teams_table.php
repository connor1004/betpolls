<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ref_id')->default(0);
            $table->unsignedBigInteger('sport_category_id')->default(0)->index();
            $table->unsignedBigInteger('sport_area_id')->default(0)->index();
            $table->string('logo')->nullable();
            $table->string('name');
            $table->string('name_es');
            $table->string('short_name');
            $table->string('short_name_es');
            $table->string('slug')->default('');
            $table->string('slug_es')->default('');
            $table->string('title')->default('');
            $table->string('title_es')->default('');
            $table->string('meta_keywords')->default('');
            $table->string('meta_keywords_es')->default('');
            $table->string('meta_description', 512)->default('');
            $table->string('meta_description_es', 512)->default('');
            $table->json('meta')->nullable();
            $table->json('meta_es')->nullable();
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
        Schema::dropIfExists('teams');
    }
}
