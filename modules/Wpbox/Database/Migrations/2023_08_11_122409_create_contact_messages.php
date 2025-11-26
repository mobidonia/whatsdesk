<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactMessages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fb_message_id')->nullable();
            $table->unsignedBigInteger('contact_id');
            $table->foreign('contact_id')->references('id')->on('contacts');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('header_text')->default("");
            $table->string('footer_text')->default("");
            $table->string('header_image')->default("");
            $table->string('header_video')->default("");
            $table->string('header_location')->default("");
            $table->string('header_document')->default("");
            $table->text('buttons');
            $table->text('value');
            $table->string('error')->default("");
            $table->boolean('is_campign_messages')->default(false);
            $table->boolean('is_message_by_contact')->default(false);
            $table->integer('message_type')->default(1)->comment("1 - text, 2-media, 3-location");
            $table->integer('status')->default(1)->comment("0-Schuduled, 1-Sending, 2-Sent, 3-Delivered, 4-Read, 5-Failed");
            $table->timestamps();
            $table->timestampTz('scchuduled_at')->nullable();
            $table->text('components');
            $table->unsignedBigInteger('campaign_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
