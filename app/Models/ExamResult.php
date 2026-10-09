<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_schedule_id',
        'siswa_id',
        'started_at',
        'finished_at',
        'total_questions',
        'correct_answers',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
