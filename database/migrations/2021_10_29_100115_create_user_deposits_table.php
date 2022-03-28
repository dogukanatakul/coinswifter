<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDepositsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_deposits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('coins_id')->unsigned();
            $table->foreign('coins_id')->references('id')->on('coins');
            $table->bigInteger('contracted_banks_id')->unsigned()->nullable()->default(null);
            $table->foreign('contracted_banks_id')->references('id')->on('contracted_banks');
            $table->bigInteger('user_banks_id')->unsigned()->nullable()->default(null);
            $table->foreign('user_banks_id')->references('id')->on('user_banks');
            $table->bigInteger('users_id')->unsigned()->nullable()->default(null);
            $table->foreign('users_id')->references('id')->on('users');
            $table->decimal('amount', 38, 22)->default(0);
            $table->string('currency')->default('TRY');
            $table->string('sender_name')->nullable()->default(null);
            $table->string('iban')->nullable()->default(null);
            $table->string('receipt_no')->nullable()->default(null);
            $table->dateTime('date')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->bigInteger('tck_no')->default(0);
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('user_deposits');
    }
}
