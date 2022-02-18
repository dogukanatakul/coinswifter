<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('name');
            $table->string('surname');
            $table->bigInteger('nationality')->unsigned();
            $table->foreign('nationality')->references('id')->on('countries');
            $table->bigInteger('tck_no')->nullable()->unique();
            $table->string('pasaport_no')->nullable()->unique();
            $table->date('birthday');
            $table->text('password');
            $table->integer('type')->default(0); // SüperAdmin, Yönetici, Kullanıcı
            $table->integer('status')->default(0); // 0: Yeni Üye | 1: Telefon Doğrulamış | 2: Mail adresi doğrulamış | 3: Kimlik Bilgilerini Doğrulamış
            $table->string('referance_code')->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
