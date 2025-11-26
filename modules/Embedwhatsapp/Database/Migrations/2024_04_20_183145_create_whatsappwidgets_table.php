<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWhatsappwidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsappwidgets', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->foreignId('company_id')->constrained();
            $table->string('logo')->nullable();
            $table->string('phone_number');
            $table->string('header_text');
            $table->string('header_subtext');
            $table->text('widget_text');
            $table->string('button_text');
            $table->string('widget_type');
            $table->string('input_field_placeholder')->nullable();
            $table->string('button_color');
            $table->string('header_color');
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
        Schema::dropIfExists('whatsappwidgets');
    }
}
