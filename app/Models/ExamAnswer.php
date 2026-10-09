<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_schedule_id',
        'siswa_id',
        'question_id',
        'answer',
        'score',
        'is_flagged',
    ];

    protected $casts = [
        'is_flagged' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class , 'siswa_id');
    }

    public function examSchedule()
    {
        return $this->belongsTo(ExamSchedule::class);
    }
}
