<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parities_id')->unsigned()->nullable()->default(null);
            $table->foreign('parities_id')->references('id')->on('parities');
            $table->bigInteger('order_transactions_id')->unsigned()->nullable()->default(null);
            $table->foreign('order_transactions_id')->references('id')->on('order_transactions');
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->bigInteger('user_coins_id')->unsigned();
            $table->foreign('user_coins_id')->references('id')->on('user_coins');
            $table->bigInteger('coins_id')->unsigned();
            $table->foreign('coins_id')->references('id')->on('coins');
            $table->decimal('amount', 38, 22); // kaç adet
            $table->decimal('price', 38, 22); // o anki satış fiyatı
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
        Schema::dropIfExists('commissions');
    }
}
