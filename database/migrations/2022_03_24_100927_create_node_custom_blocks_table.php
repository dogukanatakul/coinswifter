<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNodeCustomBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('node_custom_blocks', function (Blueprint $table) {
            $table->id();
            $table->integer('block_number');
            $table->string('network');
            $table->tinyInteger('status')->default(0); // 0: bekliyor | 1: tamamlandı | 2: gönderildi
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
        Schema::dropIfExists('node_custom_blocks');
    }
}
