<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateContactTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('contacts', function (Blueprint $table) {
                $table->string('language')->default('none');
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
       

        try {
            Schema::table('messages', function (Blueprint $table) {
                $table->string('original_message')->default('');
                $table->string('sender_name')->default('');
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
        Schema::table('', function (Blueprint $table) {

        });
    }
}
