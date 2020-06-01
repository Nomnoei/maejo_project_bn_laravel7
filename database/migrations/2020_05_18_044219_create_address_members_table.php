<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('address_members', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('house_num');
            $table->string('alley');
            $table->string('district');
            $table->string('moo');
            $table->char('status',1);
            $table->char('type_pay_id',2);
            $table->date('patment_date');
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
        Schema::dropIfExists('address_members');
    }
}
