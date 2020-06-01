<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentPicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('payment_pics', function (Blueprint $table) {
          $table->engine = 'MyISAM';
          $table->integer('id')->unsigned();
          $table->integer('address_id')->unsigned();
          $table->integer('payment_id');
          $table->string('picture');
          $table->decimal('price',7,2);
          $table->char('status',1);
          $table->timestamps();
          $table->primary(['id', 'address_id']);
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_pics');
    }
}
