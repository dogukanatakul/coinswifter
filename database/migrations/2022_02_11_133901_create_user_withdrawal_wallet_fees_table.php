<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWithdrawalWalletFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdrawal_wallet_fees', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
            $table->bigInteger('user_withdrawal_wallets_id')->unsigned();
            $table->foreign('user_withdrawal_wallets_id')->references('id')->on('user_withdrawal_wallets');
            $table->bigInteger('user_withdrawal_wallet_children_id')->unsigned();
            $table->foreign('user_withdrawal_wallet_children_id')->references('id')->on('user_withdrawal_wallet_children');
            $table->decimal('amount', 38, 22);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('user_withdrawal_wallet_fees');
    }
}
