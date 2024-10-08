<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinsTable extends Migration
{
    public function up()
    {
        Schema::create('coins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('symbol');
            $table->bigInteger('networks_id')->unsigned();
            $table->foreign('networks_id')->references('id')->on('networks');
            $table->unique(['symbol', 'networks_id', 'name'], 'token_unique');
            $table->string('contract')->nullable();
            $table->string('token_type')->nullable()->default(null);
            $table->string('status')->default('normal'); // normal | ico | preview
            $table->decimal('transfer_min', 38, 22)->default(100);
            $table->decimal('transfer_max', 38, 22)->default(9999999999999999);
            $table->decimal('commission_in', 38, 22)->default(0.15);
            $table->decimal('commission_out', 38, 22)->default(0.15);
            $table->string('commission_type')->default('percent');
            $table->json('settings')->default('{}');
            $table->json('promotion')->default('{}');
            $table->integer('order')->default(99);
            $table->text('info')->default(null)->nullable();
            $table->json('urls')->default('{}');
            $table->decimal('start_price', 38, 22)->default(0);
            $table->dateTime('start_time')->default(null)->nullable();
            $table->bigInteger('supply_max')->default(0);
            $table->bigInteger('supply_total')->default(0);
            $table->integer('precision')->default(10);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coins');
    }
}
