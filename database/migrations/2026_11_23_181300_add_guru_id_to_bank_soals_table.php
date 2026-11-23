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
        Schema::table('bank_soals', function (Blueprint $table) {
            if (!Schema::hasColumn('bank_soals', 'guru_id')) {
                $table->foreignId('guru_id')
                    ->nullable()
                    ->after('subject_id')
                    ->constrained('gurus')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_soals', function (Blueprint $table) {
            if (Schema::hasColumn('bank_soals', 'guru_id')) {
                $table->dropForeign(['guru_id']);
                $table->dropColumn('guru_id');
            }
        });
    }
};

