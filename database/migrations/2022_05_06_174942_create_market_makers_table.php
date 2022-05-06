<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketMakersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('market_makers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parities_id')->unsigned();
            $table->foreign('parities_id')->references('id')->on('parities');
            $table->bigInteger('btc_parities_id')->unsigned();
            $table->foreign('btc_parities_id')->references('id')->on('parities');
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->decimal('buy_spread', 38, 22);
            $table->decimal('sell_spread', 38, 22);
            $table->integer('buy_order_count')->default(10);
            $table->integer('sell_order_count')->default(10);
            $table->decimal('btc_buy_spread', 38, 22);
            $table->decimal('btc_sell_spread', 38, 22);
            $table->integer('btc_buy_order_count')->default(10);
            $table->integer('btc_sell_order_count')->default(10);
            $table->decimal('min_token', 38, 22);
            $table->decimal('max_token', 38, 22);
            $table->integer('scale_count')->default(10);
            $table->integer('price_scale_count')->default(10);
            $table->boolean('btc_primary')->default(false);
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
        Schema::dropIfExists('market_makers');
    }
}
