<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('payments', function (Blueprint $table) {
        $table->engine = 'MyISAM';
        $table->integer('payment_id')->unsigned();
        $table->integer('address_id')->unsigned();
        $table->decimal('price',7,2);
        $table->decimal('price_old',7,2);
        $table->date('date');
        $table->char('status',1);
        $table->timestamps();
        $table->primary(['payment_id', 'address_id']);
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
