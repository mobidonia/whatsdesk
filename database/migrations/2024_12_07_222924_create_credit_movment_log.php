<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       //Add the credits movement log
       Schema::create('credit_movement_log', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('credit_id');
        $table->foreign('credit_id')->references('id')->on('credit');

        //Also add the company id
        $table->unsignedBigInteger('company_id')->nullable();
        $table->foreign('company_id')->references('id')->on('companies');

        $table->string('action');
        $table->integer('amount');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_movement_log');
    }
};
