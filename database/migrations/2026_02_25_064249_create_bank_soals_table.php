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
        Schema::create('bank_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('option_count')->default(5);

            // Question Types Settings
            $table->integer('pg_count')->default(0);
            $table->integer('pg_percent')->default(0);
            $table->integer('pgk_count')->default(0);
            $table->integer('pgk_percent')->default(0);
            $table->integer('ls_count')->default(0);
            $table->integer('ls_percent')->default(0);
            $table->integer('mj_count')->default(0);
            $table->integer('mj_percent')->default(0);
            $table->integer('is_count')->default(0);
            $table->integer('is_percent')->default(0);
            $table->integer('ur_count')->default(0);
            $table->integer('ur_percent')->default(0);
            $table->integer('mt_count')->default(0);
            $table->integer('mt_percent')->default(0);
            $table->integer('bs_count')->default(0);
            $table->integer('bs_percent')->default(0);
            $table->integer('st_count')->default(0);
            $table->integer('st_percent')->default(0);

            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_soals');
    }
};
