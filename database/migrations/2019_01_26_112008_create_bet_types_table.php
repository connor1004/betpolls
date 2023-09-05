<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('value', ['spread', 'moneyline', 'over_under'])->index();
            $table->string('name', 180);
            $table->string('name_es', 180);
            $table->integer('win_score')->default(0);
            $table->integer('loss_score')->default(0);
            $table->integer('tie_win_score')->default(0);
            $table->integer('tie_loss_score')->default(0);
            $table->timestamps();
        });
        
        DB::table('bet_types')->insert([
            ['id'=>1, 'value'=> 'spread', 'name' => 'Spread', 'name_es' => 'Spread', 'created_at' => new \DateTime(), 'updated_at' => new \DateTime()],
            ['id'=>2, 'value'=> 'moneyline', 'name' => 'Moneyline', 'name_es' => 'Moneyline', 'created_at' => new \DateTime(), 'updated_at' => new \DateTime()],
            ['id'=>3, 'value'=> 'over_under', 'name' => 'Over/Under', 'name_es' => 'Over/Under', 'created_at' => new \DateTime(), 'updated_at' => new \DateTime()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bet_types');
    }
}
