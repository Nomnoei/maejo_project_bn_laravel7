<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->engine = 'MyISAM';
            $table->integer('id')->unsigned();
            $table->string('name');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('tel',10);
            $table->char('status',1);   //status 1 = member , status 2 = officer , status 3 = admin , status 4 = suspend account
            $table->rememberToken();
            $table->timestamps();
            $table->primary(['id', 'username']);
        });
        DB::statement('ALTER TABLE users MODIFY id INTEGER NOT NULL AUTO_INCREMENT');
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
