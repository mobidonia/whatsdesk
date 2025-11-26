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
        Schema::create('plan', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name', 191);
            $table->integer('limit_items')->comment('0 is unlimited')->default(0)->nullable();
            $table->integer('limit_orders')->comment('0 is unlimited')->default(0)->nullable();
            $table->double('price', 8, 2);
            $table->integer('period')->comment('1 - monthly, 2-anually')->default(1);
            $table->string('paddle_id', 191)->nullable();

            $table->string('description', 555)->default();
            $table->string('features', 555)->default();
            $table->integer('limit_views')->comment('0 is unlimited')->default(0);
            $table->integer('enable_ordering')->default(1);
            $table->string('stripe_id', 191)->nullable();
            $table->string('paypal_id', 191)->nullable()->default(null);
            $table->string('mollie_id', 191)->nullable()->default(null);
            $table->string('paystack_id', 191)->nullable()->default(null);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('plan_id')->nullable()->default(null);
            $table->foreign('plan_id')->references('id')->on('plan');
            $table->string('plan_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan');
    }
};
