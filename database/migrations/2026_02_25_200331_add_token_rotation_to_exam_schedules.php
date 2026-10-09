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
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->integer('token_interval')->nullable()->after('token'); // in minutes
            $table->timestamp('token_refreshed_at')->nullable()->after('token_interval');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropColumn(['token_interval', 'token_refreshed_at']);
        });
    }
};
