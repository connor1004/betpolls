<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualCandidatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manual_candidates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('category_id')->index();
            $table->unsignedInteger('country_id')->default(0)->index();
            $table->string('logo')->nullable();
            $table->unsignedInteger('candidate_type_id')->default(0)->index();
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
        Schema::dropIfExists('manual_candidates');
    }
}
