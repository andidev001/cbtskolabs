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
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_event_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_soal_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->integer('duration'); // in minutes
            $table->boolean('is_active')->default(true);
            $table->string('token', 10)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};
