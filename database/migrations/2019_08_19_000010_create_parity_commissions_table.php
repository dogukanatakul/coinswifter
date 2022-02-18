<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParityCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parity_commissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parities_id')->unsigned();
            $table->foreign('parities_id')->references('id')->on('parities');
            $table->decimal('commission', 38, 22)->default(0);
            $table->tinyInteger('type')->default(0); // 0: Yüzde | 1: Tutar
            $table->decimal('minimum', 38, 22)->default(0);
            $table->decimal('maximum', 38, 22)->default(9999999999999999);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parity_commissions');
    }
}
