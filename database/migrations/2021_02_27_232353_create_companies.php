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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('name');
            $table->string('subdomain')->nullable();
            $table->string('logo')->default('');
            $table->string('cover')->default('');
            $table->tinyInteger('active')->default(1);
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('minimum')->default(0);
            $table->string('description', 500)->default('');
            $table->double('fee', 8, 2)->default(0.00);
            $table->double('static_fee', 8, 2)->default(0.00);
            $table->tinyInteger('is_featured')->default(0);
            $table->integer('views')->default(0);
            $table->string('whatsapp_phone')->default('');
            $table->integer('do_covertion')->default(1);
            $table->string('currency')->nullable();
            $table->string('payment_info')->nullable();
            $table->string('mollie_payment_key')->nullable();
            $table->foreignId('user_id')->nullable()->index();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->default(null);
            $table->foreign('company_id')->references('id')->on('companies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
