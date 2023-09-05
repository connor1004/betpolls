<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('title_es')->default('');
            $table->string('slug');
            $table->string('slug_es')->default('');
            $table->text('content')->nullable();
            $table->text('content_es')->nullable();
            $table->string('post_type')->default('page');
            $table->string('excerpt')->nullable();
            $table->string('excerpt_es')->nullable();
            $table->string('meta_keywords')->default('');
            $table->string('meta_keywords_es')->default('');
            $table->string('meta_description')->default('');
            $table->string('meta_description_es')->default('');
            $table->unsignedBigInteger('author_id')->default(0);
            $table->string('featured_image')->nullable();
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
        Schema::dropIfExists('posts');
    }
}
