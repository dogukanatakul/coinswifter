<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parities_id')->unsigned();
            $table->foreign('parities_id')->references('id')->on('parities');

            $table->bigInteger('buyer_user_id')->unsigned();
            $table->foreign('buyer_user_id')->references('id')->on('users');

            $table->bigInteger('seller_user_id')->unsigned();
            $table->foreign('seller_user_id')->references('id')->on('users');

            $table->bigInteger('buyer_order_id')->unsigned();
            $table->foreign('buyer_order_id')->references('id')->on('orders');

            $table->bigInteger('seller_order_id')->unsigned();
            $table->foreign('seller_order_id')->references('id')->on('orders');
            $table->decimal('price', 38, 22); // toplam tutar
            $table->decimal('amount', 38, 22); // toplam tutar
            $table->bigInteger('microtime')->unique(); // round(microtime(true) * 1000) / microtime cinsinden emir gerçekleşme zamanı
            $table->softDeletes();
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
        Schema::dropIfExists('order_transactions');
    }
}
