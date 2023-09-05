<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlugAndSlugEsToManualPollPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_poll_pages', function (Blueprint $table) {
            $table->string('slug')->default('');
            $table->string('slug_es')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manual_poll_pages', function (Blueprint $table) {
            $table->dropColumn(['slug', 'slug_es']);
        });
    }
}
