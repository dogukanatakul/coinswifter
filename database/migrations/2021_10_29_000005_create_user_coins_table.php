<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserCoinsTable extends Migration
{
    public function up()
    {
        Schema::create('user_coins', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->bigInteger('user_wallets_id')->unsigned();
            $table->foreign('user_wallets_id')->references('id')->on('user_wallets');
            $table->bigInteger('coins_id')->unsigned();
            $table->foreign('coins_id')->references('id')->on('coins');
            $table->decimal('balance_pure', 38, 22)->default(0);
            $table->decimal('balance', 38, 22)->default(0);
            $table->unique(['users_id', 'user_wallets_id', 'coins_id'], 'user_coin_unique');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_coins');
    }
}
