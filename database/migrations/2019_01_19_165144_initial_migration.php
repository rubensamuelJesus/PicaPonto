<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitialMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('type', ['admin', 'client'])->default('client');
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('access_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rfid_id')->references('rfid_id')->on('access_id');
            $table->timestamps();
        });

        Schema::create('access_id', function (Blueprint $table) {
            $table->increments('id');
            $table->string('rfid_id')->unique();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('sensors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('controller_id');//////////////////////////////////////
            $table->string('name')->unique();
            $table->integer('value')->default(0);
        });

        Schema::create('user_sensors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('sensor_id')->unsigned();
            $table->foreign('sensor_id')->references('id')->on('sensors');
        });
/*
        Schema::create('controllers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip')->unique();
            $table->string('name')->unique();
        });*/

    }

    public function down()
    {
        Schema::dropIfExists('user_sensors');
        Schema::dropIfExists('sensors');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('access_history');
        Schema::dropIfExists('access_id');
        Schema::dropIfExists('users');
    }
}
