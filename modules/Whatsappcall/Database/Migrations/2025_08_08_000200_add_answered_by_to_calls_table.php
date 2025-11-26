<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->unsignedBigInteger('answered_by')->nullable()->after('answered_at');
            $table->index('answered_by');
            // Optionally add FK if users table exists and you want constraint
            // $table->foreign('answered_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            // if FK added above, drop it first
            // $table->dropForeign(['answered_by']);
            $table->dropColumn('answered_by');
        });
    }
};

