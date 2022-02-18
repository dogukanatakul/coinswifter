<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractedBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracted_banks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('banks_id')->unsigned();
            $table->foreign('banks_id')->references('id')->on('banks');
            $table->string('account_name')->nullable()->default(null);
            $table->string('iban')->nullable()->default(null);
            $table->string('account_number')->nullable()->default(null);
            $table->string('branch_code')->nullable()->default(null);
            $table->string('account_type')->default('TRY'); // TRY - USDT
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
        Schema::dropIfExists('contracted_banks');
    }
}
