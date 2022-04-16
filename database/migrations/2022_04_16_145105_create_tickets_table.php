<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id')->unsigned();
            $table->foreign('users_id')->references('id')->on('users');
            $table->bigInteger('user_answered_id')->nullable()->unsigned();
            $table->foreign('user_answered_id')->references('id')->on('users');
            $table->bigInteger('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('ticket_categories');
            $table->bigInteger('issue_id')->unsigned();
            $table->foreign('issue_id')->references('id')->on('ticket_issues');
            $table->text('detail');
            $table->string('file_name')->nullable();
            $table->string('file_extension')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('tickets');
    }
}
