<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('websupportwidgets', function (Blueprint $table) {
            $table->boolean('call_enabled')->default(false)->after('help_show_search');
            $table->string('call_info_message')->nullable()->after('call_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websupportwidgets', function (Blueprint $table) {
            $table->dropColumn(['call_enabled', 'call_info_message']);
        });
    }
};


