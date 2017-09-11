<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $tbl) {
            $tbl->increments('id');
            $tbl->integer('user_id');
            $tbl->integer('conversation_id');
            $tbl->text('message');
            $tbl->text('message_html')->nullable();
            $tbl->enum('message_type', ['text', 'image', 'file', 'audio']);
            $tbl->boolean('is_seen')->default(0);
            $tbl->boolean('deleted_from_sender')->default(0);
            $tbl->boolean('deleted_from_receiver')->default(0);
            $tbl->boolean( 'push_status' )->default( 0 )->comment( '0=ignore, 1=send-in-push' )->nullable();
            $tbl->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::drop('messages');
    }
}
