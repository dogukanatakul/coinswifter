<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBanksTable extends Migration
{
    public function up()
    {
        Schema::create('user_banks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->bigInteger('banks_id')->unsigned();
            $table->foreign('banks_id')->references('id')->on('banks');
            $table->integer('branch_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->bigInteger('account_number')->nullable();
            $table->text('iban')->unique();
            $table->boolean('primary')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_banks');
    }
}
