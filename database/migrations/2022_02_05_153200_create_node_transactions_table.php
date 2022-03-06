<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodeTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('block_number')->default(0);
            $table->string('from');
            $table->string('to');
            $table->string('contract')->default(null)->nullable();
            $table->string('txh');
            $table->decimal('fee', 38, 22)->default(0);
            $table->decimal('value', 38, 22)->default(0);
            $table->string('progress'); // in - out
            $table->string('network'); // BSC - ETH
            $table->tinyInteger('status')->default(0); // 0: başarılı 1:başarısız
            $table->tinyInteger('processed')->default(0); // 0: bekliyor 1:okundu
            $table->unique(['block_number', 'txh', 'from', 'to', 'value', 'network'], 'transaction_unique');
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
        Schema::dropIfExists('node_transactions');
    }
}
