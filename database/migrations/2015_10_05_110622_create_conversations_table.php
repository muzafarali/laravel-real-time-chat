<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $tbl) {
            $tbl->increments('id');
            $tbl->integer('user_one');
            $tbl->integer('user_two');
            $tbl->integer('booking_id');
            $tbl->integer('car_id');
            $tbl->boolean('status');
            $tbl->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('conversations');
    }
}
