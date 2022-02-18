<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('source_coin_id')->unsigned();
            $table->foreign('source_coin_id')->references('id')->on('coins');
            $table->bigInteger('coin_id')->unsigned();
            $table->foreign('coin_id')->references('id')->on('coins');
            $table->integer('order')->default(99);
            $table->string('status')->default('normal'); // normal | ico | preview
            $table->json('settings')->default('{}');
            $table->json('promotion')->default('{}');
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
        Schema::dropIfExists('parities');
    }
}
