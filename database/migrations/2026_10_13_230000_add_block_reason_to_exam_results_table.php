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
        if (Schema::hasTable('exam_results') && !Schema::hasColumn('exam_results', 'block_reason')) {
            Schema::table('exam_results', function (Blueprint $table) {
                $table->string('block_reason')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exam_results') && Schema::hasColumn('exam_results', 'block_reason')) {
            Schema::table('exam_results', function (Blueprint $table) {
                $table->dropColumn('block_reason');
            });
        }
    }
};

