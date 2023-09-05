<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueIndexInPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropUnique('SECONDARY');
            $table->unique(['user_id', 'type', 'sport_category_id', 'league_id', 'start_at'], 'SECONDARY');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('points', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropUnique('SECONDARY');
            $table->unique(['user_id', 'sport_category_id', 'league_id', 'start_at'], 'SECONDARY');
        });
    }
}
