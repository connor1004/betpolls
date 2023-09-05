<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('country', 10)->default('DO');
            $table->enum('role', ['unknown', 'voter', 'admin'])->default('voter');
            $table->boolean('confirmed')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('users')->insert([
            [
                'id'=>1, 'firstname'=> 'Nick', 'lastname' => 'Jang', 'username' => 'nickjang234',
                'email' => 'nickjang234@gmail.com', 'role' => 'admin', 'confirmed' => 1, 'password' => '$2y$10$Vh8W4psafpy3DDs2FhFEWe.SP16LiB5Ukj5H9LrOURzsOFa2PRjbW',
                'created_at' => new \Carbon\Carbon(),
                'updated_at' => new \Carbon\Carbon()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
