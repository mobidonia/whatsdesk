<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRepliesTableSecond extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       try {
            //Update the replies table
            Schema::table('replies', function (Blueprint $table) {
                $table->string('button_name')->default("");
                $table->string('button_url')->default("");
            });
       } catch (\Throwable $th) {
        //throw $th;
       }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
