<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointValuesToManualEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manual_events', function (Blueprint $table) {
            $table->integer('spread_win_points')->default(0);
            $table->integer('spread_loss_points')->default(0);
            $table->integer('moneyline1_win_points')->default(0);
            $table->integer('moneyline1_loss_points')->default(0);
            $table->integer('moneyline2_win_points')->default(0);
            $table->integer('moneyline2_loss_points')->default(0);
            $table->integer('moneyline_tie_win_points')->default(0);
            $table->integer('moneyline_tie_loss_points')->default(0);
            $table->integer('over_under_win_points')->default(0);
            $table->integer('over_under_loss_points')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manual_events', function (Blueprint $table) {
            $table->dropColumn([
                'spread_win_points', 'spread_loss_points',
                'moneyline1_win_points', 'moneyline1_loss_points',
                'moneyline2_win_points', 'moneyline2_loss_points',
                'moneyline_tie_win_points', 'moneyline_tie_loss_points',
                'over_under_win_points', 'over_under_loss_points',
            ]);
        });
    }
}
