<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeoipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('geoips', function (Blueprint $table) {
            $table->string('ip')->primary();
            $table->string('continent_code')->nullable();
            $table->string('continent_name')->nullable();
            $table->string('country_code2')->nullable();
            $table->string('country_code3')->nullable();
            $table->string('country_name')->nullable();
            $table->string('country_capital')->nullable();
            $table->string('state_prov')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('calling_code')->nullable();
            $table->string('country_tld')->nullable();
            $table->string('languages')->nullable();
            $table->string('country_flag')->nullable();
            $table->string('isp')->nullable();
            $table->string('connection_type')->nullable();
            $table->string('organization')->nullable();
            $table->string('geoname_id')->nullable();
            $table->string('currency')->nullable();
            $table->string('time_zone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('geoips');
    }
}
