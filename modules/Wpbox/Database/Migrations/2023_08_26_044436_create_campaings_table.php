<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wa_campaings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('send_to')->default(1);
            $table->integer('sended_to')->default(0);
            $table->integer('delivered_to')->default(0);
            $table->integer('read_by')->default(0);
            $table->integer('total_contacts')->default(0);
            $table->string('timestamp_for_delivery')->nullable();
            $table->text('variables');
            $table->text('variables_match');
            $table->string('media_link')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreign('template_id')->references('id')->on('wa_templates');
            $table->unsignedBigInteger('group_id')->nullable();
            $table->foreign('group_id')->references('id')->on('groups');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->foreign('contact_id')->references('id')->on('contacts');
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
        Schema::dropIfExists('wa_campaings');
    }
}
