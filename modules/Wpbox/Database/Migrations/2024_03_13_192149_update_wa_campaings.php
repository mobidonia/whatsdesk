<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWaCampaings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('wa_campaings', function (Blueprint $table) {
            $table->boolean('is_api')->default(false)->comment("Flag to represent if the campaign is for API");
            $table->boolean('is_active')->default(true)->comment("Flag to represent if the campaign is active or paused");
        });

        //Update the replies table
        Schema::table('replies', function (Blueprint $table) {
            $table->string('button_name')->default("");
            $table->string('button_url')->default("");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('', function (Blueprint $table) {

        });
    }
}
