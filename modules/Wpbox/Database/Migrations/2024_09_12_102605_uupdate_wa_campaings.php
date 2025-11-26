<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UupdateWaCampaings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('wa_campaings', function (Blueprint $table) {
                $table->boolean('is_reminder')->default(false)->comment("Flag to represent if the campaign is for Reminder");
            });
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
