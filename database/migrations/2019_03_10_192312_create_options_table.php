<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionsTable extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('options', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name')->length(50)->unique();
      $table->text('value')->nullable();
      $table->timestamps();
    });

    \DB::table('options')->insert([
      [
        'name' => 'settings', 
        'value'=> serialize([
          'google_data_ad_client' => 'ca-pub-123456788910',
          'google_data_ad_slot' => '123456789'
        ])
      ],
    ]);
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::dropIfExists('options');
  }
}