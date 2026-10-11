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
        Schema::table('exam_results', function (Blueprint $table) {
            $table->index(['exam_schedule_id', 'status'], 'idx_res_sched_status');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index(['bank_soal_id'], 'idx_q_bank_soal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_results', function (Blueprint $table) {
            $table->dropIndex('idx_res_sched_status');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_q_bank_soal');
        });
    }
};
