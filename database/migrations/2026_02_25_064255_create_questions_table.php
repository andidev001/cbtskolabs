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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soals')->onDelete('cascade');
            $table->longText('question_text');
            $table->longText('option_a')->nullable();
            $table->longText('option_b')->nullable();
            $table->longText('option_c')->nullable();
            $table->longText('option_d')->nullable();
            $table->longText('option_e')->nullable();
            $table->string('answer')->nullable();
            $table->enum('type', ['PG', 'Essay'])->default('PG');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
