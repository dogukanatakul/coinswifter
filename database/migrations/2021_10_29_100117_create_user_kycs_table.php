<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserKycsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_kycs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->bigInteger('user_addresses_id')->unsigned();
            $table->foreign('user_addresses_id')->references('id')->on('user_addresses');
            $table->string('file_name');
            $table->string('file_extension');
            $table->bigInteger('file_size');
            $table->bigInteger('confirming_user_id')->unsigned()->nullable()->default(null);
            $table->foreign('confirming_user_id')->references('id')->on('users');
            $table->tinyInteger('status')->default(0); // 0: waiting - 1: Confirm - 3: Rejection
            $table->text('explanation')->nullable()->default(null);
            $table->string('type');
            $table->unique(['users_id', 'user_addresses_id', 'type'], 'kyc_unique');
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
        Schema::dropIfExists('user_kyc');
    }
}
