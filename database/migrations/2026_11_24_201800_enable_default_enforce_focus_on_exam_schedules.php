<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('exam_schedules', 'enforce_focus')) {
            DB::table('exam_schedules')->update(['enforce_focus' => 1]);
            try {
                DB::statement('ALTER TABLE exam_schedules MODIFY enforce_focus TINYINT(1) NOT NULL DEFAULT 1');
            } catch (\Throwable $e) {
                // Ignore if driver doesn't support MODIFY syntax
            }
        }
    }

    public function down(): void
    {
        //
    }
};
