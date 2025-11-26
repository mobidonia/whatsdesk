<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->string('direction'); // UIC or BIC
            $table->string('status')->default('initiated'); // initiated, ringing, in_progress, ended, missed, declined, failed
            $table->string('wa_call_id')->nullable(); // if provided in future meta callbacks
            $table->string('wa_user_id')->nullable();
            $table->timestamp('started_at')->default(now());
            $table->timestamp('answered_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_calls');
    }
};

