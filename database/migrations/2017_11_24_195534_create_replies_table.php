<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::enableForeignKeyConstraints();
        
        Schema::create('replies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('thread_id');
            $table->integer('user_id');
            $table->text('body');            
            $table->timestamps();
            //$table->foreign('thread_id')->references('id')->on('threads')->onDelete('cascade'); //some people aren't huge fans of adding constraints at that level
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replies');
        
        //Schema::table('posts', function(Blueprint $table){
        //    $table->dropForeign('posts_user_id_foreign');
        //    $table->dropForeign(['user_id']);
        //    $table->dropIndex(['table2field']);                
        //    $table->dropColumn('user_id');
        //});
    }
}
