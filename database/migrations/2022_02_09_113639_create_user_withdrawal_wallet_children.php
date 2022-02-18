<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWithdrawalWalletChildren extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdrawal_wallet_children', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_withdrawal_wallets_id')->unsigned();
            $table->foreign('user_withdrawal_wallets_id')->references('id')->on('user_withdrawal_wallets');
            $table->bigInteger('user_coins_id')->unsigned();
            $table->foreign('user_coins_id')->references('id')->on('user_coins');
            $table->decimal('amount', 38, 22);
            $table->tinyInteger('status')->default(0); // 0: bekliyor | 1: Gönderildi | 3: Hata
            $table->string('txh')->default(null)->nullable();
            $table->json('error_answer')->default(null)->nullable();
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
        Schema::dropIfExists('user_withdrawal_wallet_children');
    }
}
