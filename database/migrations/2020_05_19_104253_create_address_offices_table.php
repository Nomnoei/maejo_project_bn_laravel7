<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('address_offices', function (Blueprint $table) {
          $table->increments('id');
          $table->integer('user_id');
          $table->text('house');
          $table->char('status',1); // status 1 = มีในระบบ,status 2 = ลบ
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
        Schema::dropIfExists('address_offices');
    }
}
