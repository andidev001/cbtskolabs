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
            $table->boolean('show_score')->default(false)->after('token');
            $table->boolean('show_percentage')->default(false)->after('show_score');
            $table->boolean('show_answer')->default(false)->after('show_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropColumn(['show_score', 'show_percentage', 'show_answer']);
        });
    }
};
