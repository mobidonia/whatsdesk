<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsupportwidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('websupportwidgets', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->foreignId('company_id')->constrained();

            // General widget settings
            $table->string('logo')->nullable();
            $table->string('company_name');
            $table->string('welcome_message');
            $table->string('primary_color')->default('#4F46E5');
            $table->string('secondary_color')->default('#6366F1');
            $table->string('position')->default('bottom-right'); // bottom-right, bottom-left

            // Chat tab settings
            $table->boolean('chat_enabled')->default(true);
            $table->string('whatsapp_number')->nullable();
            $table->string('chat_welcome_message')->nullable();
            $table->string('chat_button_text')->default('Start Chat');

            // Email tab settings
            $table->boolean('email_enabled')->default(true);
            $table->string('email_recipient')->nullable();
            $table->string('email_subject_prefix')->default('Contact Form');
            $table->string('email_welcome_message')->nullable();
            $table->string('email_success_message')->default('Thank you! We will get back to you soon.');

            // Help tab settings
            $table->boolean('help_enabled')->default(true);
            $table->string('help_welcome_message')->nullable();
            $table->integer('help_articles_limit')->default(5);
            $table->boolean('help_show_search')->default(true);

            // Advanced settings
            $table->boolean('show_company_logo')->default(true);
            $table->boolean('show_agent_status')->default(true);
            $table->string('offline_message')->nullable();
            $table->json('business_hours')->nullable();
            $table->string('timezone')->default('UTC');

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
        Schema::dropIfExists('websupportwidgets');
    }
}
