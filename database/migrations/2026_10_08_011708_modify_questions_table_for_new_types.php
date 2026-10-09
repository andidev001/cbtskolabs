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
        // Ubah enum menjadi varchar agar lebih fleksibel menggunakan raw SQL (tanpa perlu doctrine/dbal)
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE questions MODIFY COLUMN type VARCHAR(50) DEFAULT 'PG'");
        
        Schema::table('questions', function (Blueprint $table) {
            $table->json('complex_options')->nullable()->after('answer')->comment('Untuk menyimpan data menjodohkan, mengurutkan, dll');
            $table->string('audio_path')->nullable()->after('complex_options')->comment('Untuk soal listening');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['complex_options', 'audio_path']);
        });
        
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE questions MODIFY COLUMN type ENUM('PG', 'Essay') DEFAULT 'PG'");
    }
};
