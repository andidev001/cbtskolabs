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
        'status',
        'block_reason',
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

    public static function calculateScoreAndFinish($scheduleId, $siswaId)
    {
        $schedule = \App\Models\ExamSchedule::with('bankSoal')->find($scheduleId);
        if (!$schedule) return null;

        $questions = \App\Models\Question::where('bank_soal_id', $schedule->bank_soal_id)
            ->leftJoin('exam_answers', function ($j) use ($scheduleId, $siswaId) {
                $j->on('questions.id', '=', 'exam_answers.question_id')
                  ->where('exam_answers.exam_schedule_id', $scheduleId)
                  ->where('exam_answers.siswa_id', $siswaId);
            })
            ->select('questions.id', 'questions.type', 'questions.answer as cor_ans', 'questions.complex_options', 'exam_answers.answer as stu_ans', 'exam_answers.score as es_score')
            ->get();

        $totalScore = 0;
        $correctPG = 0;
        $totalQ = $questions->count();
        $grouped = $questions->groupBy('type');
        
        $typePercentMap = [
            'PG' => 'pg_percent', 'PGK' => 'pgk_percent', 'LS' => 'ls_percent',
            'MJ' => 'mj_percent', 'IS' => 'is_percent', 'UR' => 'ur_percent',
            'Essay' => 'ur_percent', 'MT' => 'mt_percent', 'BS' => 'bs_percent', 'ST' => 'st_percent'
        ];

        foreach ($grouped as $type => $qs) {
            $count = $qs->count();
            if ($count === 0) continue;
            
            $pctField = $typePercentMap[$type] ?? null;
            $percent = $pctField ? ($schedule->bankSoal->$pctField ?? 0) : 0;
            $earned = 0;
            
            foreach ($qs as $q) {
                $stuAns = $q->stu_ans ?? '';
                $corAns = $q->cor_ans ?? '';
                
                if (in_array($type, ['PG', 'LS', 'BS', 'ST'])) {
                    if (strtoupper($stuAns) === strtoupper($corAns) && $stuAns !== '') {
                        $earned++;
                        if ($type == 'PG') $correctPG++;
                    }
                } elseif ($type == 'IS') {
                    if (strtolower(trim($stuAns)) === strtolower(trim($corAns)) && $stuAns !== '') {
                        $earned++;
                    }
                } elseif ($type == 'PGK') {
                    $stuArr = json_decode($stuAns, true) ?: [];
                    $corArr = json_decode($corAns, true) ?: [];
                    if (count($stuArr) > 0 && empty(array_diff($corArr, $stuArr)) && empty(array_diff($stuArr, $corArr))) {
                        $earned++;
                    }
                } elseif ($type == 'MJ') {
                    $stuMap = json_decode($stuAns, true) ?: [];
                    $corOpts = json_decode($q->complex_options ?? '{}', true);
                    $corLeft = $corOpts['left'] ?? [];
                    $corRight = $corOpts['right'] ?? [];
                    $correctPairs = 0;
                    $totalPairs = count($corLeft);
                    
                    if ($totalPairs > 0 && count($stuMap) > 0) {
                        foreach ($corLeft as $idx => $leftItem) {
                            if (isset($stuMap[$idx]) && $stuMap[$idx] === ($corRight[$idx] ?? '')) {
                                $correctPairs++;
                            }
                        }
                        $earned += ($correctPairs / $totalPairs);
                    }
                } elseif ($type == 'MT') {
                    $stuItems = json_decode($stuAns, true) ?: [];
                    $corOpts = json_decode($q->complex_options ?? '{}', true);
                    $corItems = $corOpts['items'] ?? [];
                    
                    if (count($stuItems) > 0 && $stuItems === $corItems) {
                        $earned++;
                    }
                } elseif (in_array($type, ['UR', 'Essay'])) {
                    $earned += ($q->es_score ?: 0) / 100;
                }
            }
            
            if ($percent > 0) {
                $totalScore += ($earned / $count) * $percent;
            }
        }

        $score = round($totalScore, 1);
        return self::updateOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswaId], 
            ['finished_at' => now(), 'status' => 'finished', 'total_questions' => $totalQ, 'correct_answers' => $correctPG, 'score' => $score]
        );
    }
}
