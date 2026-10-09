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
        Schema::table('siswas', function (Blueprint $table) {
            $table->string('nomer_ujian')->unique()->after('id');
            $table->unsignedBigInteger('religion_id')->nullable()->after('nisn');
            $table->unsignedBigInteger('jurusan_id')->nullable()->after('religion_id');
            $table->unsignedBigInteger('group_id')->nullable()->after('jurusan_id');
            $table->string('sesi')->nullable()->after('group_id');
            $table->boolean('status')->default(1)->after('sesi');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn(['nomer_ujian', 'religion_id', 'jurusan_id', 'group_id', 'sesi', 'status']);
        });
    }
};
