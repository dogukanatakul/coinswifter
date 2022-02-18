<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->bigInteger('parities_id')->unsigned();
            $table->foreign('parities_id')->references('id')->on('parities');
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->decimal('trigger', 38, 22)->default(0);
            $table->decimal('price', 38, 22)->default(0);
            $table->decimal('amount', 38, 22);
            $table->integer('percent')->default(0);
            $table->decimal('total', 38, 22);
            $table->string('type');
            $table->string('process'); // buy - Alış | sell - Satış
            $table->bigInteger('microtime')->unique(); // round(microtime(true) * 1000) / microtime cinsinden emir verme zamanı
            $table->softDeletes();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE orders ALTER COLUMN uuid SET DEFAULT uuid_generate_v4();');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
