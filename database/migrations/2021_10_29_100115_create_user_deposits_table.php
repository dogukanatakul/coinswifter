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
            $table->bigInteger('user_banks_id')->unsigned();
            $table->foreign('user_banks_id')->references('id')->on('user_banks');
            $table->decimal('amount', 38, 22);
            $table->string('unique_token');
            $table->string('bank_name');
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
