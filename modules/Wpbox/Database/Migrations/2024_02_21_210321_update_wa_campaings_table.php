<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWaCampaingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wa_campaings', function (Blueprint $table) {
            $table->integer('bot_type')->default(2)->comment("2-on exact match, 3-on contains");
            $table->string('trigger')->default("");
            $table->integer('used')->default(0);
            $table->boolean('is_bot_active')->default(true)->comment("Flag to represent if the bot is active or not");
            $table->boolean('is_bot')->default(false)->comment("Flag to represent if the campaign is for bots");
            
        });

        //Update the replies table
        Schema::table('replies', function (Blueprint $table) {
            $table->string('header')->default("");
            $table->string('footer')->default("");
            $table->string('button1')->default("");
            $table->string('button1_id')->default("");
            $table->string('button2')->default("");
            $table->string('button2_id')->default("");
            $table->string('button3')->default("");
            $table->string('button3_id')->default("");
        });

        //Update the messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('bot_has_replied')->default(false);
            $table->boolean('ai_bot_has_replied')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('wa_campaings', function (Blueprint $table) {
        });
    }
}