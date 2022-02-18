<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWithdrawalWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdrawal_wallets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->bigInteger('to_user_id')->unsigned()->nullable();
            $table->foreign('to_user_id')->references('id')->on('users');
            $table->bigInteger('user_coins_id')->unsigned()->nullable();
            $table->foreign('user_coins_id')->references('id')->on('user_coins');
            $table->bigInteger('coins_id')->unsigned()->nullable();
            $table->foreign('coins_id')->references('id')->on('coins');
            $table->decimal('amount', 38, 22);
            $table->decimal('send_amount', 38, 22);
            $table->decimal('commission', 38, 22);
            $table->string('to');
            $table->tinyInteger('status')->default(0); // 1: Bekliyor | 2: Gönderildi | 3: Gönderecek cüzdan bulunamadı veya fazla fee gerektiriyor.
            $table->softDeletes();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE user_withdrawal_wallets ALTER COLUMN uuid SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_withdrawal_wallets');
    }
}
