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
        //Update plans table   
        Schema::table('plan', function (Blueprint $table) {
            $table->integer('credit_amount')->default(0)->after('price');
        });

        //Create the cost table
        Schema::create('action_credit_cost', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->integer('cost');
            $table->timestamps();
        });

        Schema::create('credit', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->bigInteger('credit_amount')->comment('Total credits allocated');
            $table->bigInteger('used_credit_amount')->default(0)->comment('Credits that have been spent');
            $table->bigInteger('remaining_credit_amount')->default(0)->comment('Credits still available');
            $table->date('expiration_date')->nullable()->comment('Date when credits expire');
            $table->string('source')->nullable()->comment('Where credits came from (e.g. purchase, bonus)');
            $table->softDeletes(); // Add soft deletes for historical tracking
            $table->timestamps();
            
            // Add index for faster lookups
            $table->index(['company_id', 'expiration_date']);
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits_system');
    }
};
