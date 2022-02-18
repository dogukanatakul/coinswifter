<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->bigInteger('user_banks_id')->unsigned()->nullable();
            $table->foreign('user_banks_id')->references('id')->on('user_banks');
            $table->bigInteger('user_coins_id')->unsigned();
            $table->foreign('user_coins_id')->references('id')->on('user_coins');
            $table->decimal('amount', 38, 22)->default(0);
            $table->text('explanation')->nullable()->default(null);
            $table->tinyInteger('status')->default(0); // 0: waiting - 1: Confirm - 3: Rejection
            $table->softDeletes();
            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::statement('ALTER TABLE user_withdrawals ALTER COLUMN uuid SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_withdrawals');
    }
}
