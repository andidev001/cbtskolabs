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

    /**
     * Evaluasi skor 1 butir soal (skala 0.0 s.d. 1.0) untuk semua 9 tipe soal:
     * PG, PGK, LS, MJ, IS, UR/Essay, MT, BS, ST.
     */
    public static function evaluateQuestionScore(string $rawType, $corAns, $complexOptions, $stuAns, $esScore = null): float
    {
        $type = strtoupper(trim($rawType));
        if ($type === 'ESSAY') {
            $type = 'UR';
        }

        $stuStr = is_string($stuAns) ? trim($stuAns) : (is_null($stuAns) ? '' : json_encode($stuAns));
        $corStr = is_string($corAns) ? trim($corAns) : (is_null($corAns) ? '' : json_encode($corAns));

        if (in_array($type, ['PG', 'LS', 'BS', 'ST'], true)) {
            if ($stuStr === '') {
                return 0.0;
            }
            $s = strtoupper($stuStr);
            $c = strtoupper($corStr);

            if (in_array($type, ['BS', 'ST'], true)) {
                if (in_array($s, ['BENAR', 'SETUJU'], true)) $s = 'A';
                elseif (in_array($s, ['SALAH', 'TIDAK SETUJU', 'TIDAK'], true)) $s = 'B';

                if (in_array($c, ['BENAR', 'SETUJU'], true)) $c = 'A';
                elseif (in_array($c, ['SALAH', 'TIDAK SETUJU', 'TIDAK'], true)) $c = 'B';
            }

            return ($s !== '' && $s === $c) ? 1.0 : 0.0;
        }

        if ($type === 'IS') {
            if ($stuStr === '' || $corStr === '') {
                return 0.0;
            }
            $normStu = mb_strtolower(trim(preg_replace('/\s+/', ' ', strip_tags($stuStr))), 'UTF-8');
            $normCor = mb_strtolower(trim(preg_replace('/\s+/', ' ', strip_tags($corStr))), 'UTF-8');
            return ($normStu !== '' && $normStu === $normCor) ? 1.0 : 0.0;
        }

        if ($type === 'PGK') {
            $stuArr = is_array($stuAns) ? $stuAns : (json_decode($stuStr, true) ?: (strlen($stuStr) ? explode(',', $stuStr) : []));
            $corArr = is_array($corAns) ? $corAns : (json_decode($corStr, true) ?: (strlen($corStr) ? explode(',', $corStr) : []));

            $stuNorm = array_values(array_unique(array_filter(array_map(fn($v) => strtolower(trim((string) $v)), (array) $stuArr))));
            $corNorm = array_values(array_unique(array_filter(array_map(fn($v) => strtolower(trim((string) $v)), (array) $corArr))));

            if (count($corNorm) > 0 && count($stuNorm) === count($corNorm) && empty(array_diff($corNorm, $stuNorm)) && empty(array_diff($stuNorm, $corNorm))) {
                return 1.0;
            }
            return 0.0;
        }

        if ($type === 'MJ') {
            $stuMap = is_array($stuAns) ? $stuAns : (json_decode($stuStr, true) ?: []);
            $corOpts = is_array($complexOptions) ? $complexOptions : (json_decode((string) ($complexOptions ?? '{}'), true) ?: []);
            $corLeft = $corOpts['left'] ?? [];
            $corRight = $corOpts['right'] ?? [];
            $totalPairs = count($corLeft);

            if ($totalPairs === 0 || empty($stuMap) || !is_array($stuMap)) {
                return 0.0;
            }

            $correctPairs = 0;
            foreach ($corLeft as $idx => $leftItem) {
                $stuVal = isset($stuMap[$idx]) ? trim((string) $stuMap[$idx]) : '';
                $corVal = isset($corRight[$idx]) ? trim((string) $corRight[$idx]) : '';
                if ($stuVal !== '' && $stuVal === $corVal) {
                    $correctPairs++;
                }
            }

            return $correctPairs / $totalPairs;
        }

        if ($type === 'MT') {
            $stuItems = is_array($stuAns) ? $stuAns : (json_decode($stuStr, true) ?: []);
            $corOpts = is_array($complexOptions) ? $complexOptions : (json_decode((string) ($complexOptions ?? '{}'), true) ?: []);
            $corItems = $corOpts['items'] ?? [];

            if (empty($corItems) || empty($stuItems) || !is_array($stuItems)) {
                return 0.0;
            }

            $stuNorm = array_values(array_map(fn($v) => trim((string) $v), $stuItems));
            $corNorm = array_values(array_map(fn($v) => trim((string) $v), $corItems));

            return ($stuNorm === $corNorm) ? 1.0 : 0.0;
        }

        if ($type === 'UR') {
            $val = (float) ($esScore ?: 0);
            return max(0.0, min(1.0, $val / 100.0));
        }

        return 0.0;
    }

    /**
     * Cek apakah jawaban siswa pada suatu butir soal dianggap benar (untuk Analisis Butir Soal).
     */
    public static function isAnswerCorrect($question, $examAnswer): bool
    {
        if (!$examAnswer) {
            return false;
        }
        $ratio = self::evaluateQuestionScore(
            (string) $question->type,
            $question->answer,
            $question->complex_options,
            $examAnswer->answer,
            $examAnswer->score
        );
        return $ratio >= 0.5;
    }

    /**
     * Hitung total_questions, correct_answers, dan score berdasarkan bobot persen Bank Soal.
     */
    public static function computeMetricsForStudent($scheduleId, $siswaId): ?array
    {
        $schedule = ExamSchedule::with('bankSoal')->find($scheduleId);
        if (!$schedule || !$schedule->bankSoal) {
            return null;
        }

        $questions = Question::where('bank_soal_id', $schedule->bank_soal_id)
            ->leftJoin('exam_answers', function ($j) use ($scheduleId, $siswaId) {
                $j->on('questions.id', '=', 'exam_answers.question_id')
                  ->where('exam_answers.exam_schedule_id', $scheduleId)
                  ->where('exam_answers.siswa_id', $siswaId);
            })
            ->select(
                'questions.id',
                'questions.type',
                'questions.answer as cor_ans',
                'questions.complex_options',
                'exam_answers.answer as stu_ans',
                'exam_answers.score as es_score'
            )
            ->get();

        $totalQ = $questions->count();
        if ($totalQ === 0) {
            return [
                'total_questions' => 0,
                'correct_answers' => 0,
                'score' => 0.0,
            ];
        }

        // Normalisasi tipe 'Essay' menjadi 'UR' sebelum dikelompokkan agar ur_percent tidak dihitung ganda
        $grouped = $questions->groupBy(function ($q) {
            $t = strtoupper(trim((string) $q->type));
            return $t === 'ESSAY' ? 'UR' : $t;
        });

        $typePercentMap = [
            'PG'  => 'pg_percent',
            'PGK' => 'pgk_percent',
            'LS'  => 'ls_percent',
            'MJ'  => 'mj_percent',
            'IS'  => 'is_percent',
            'UR'  => 'ur_percent',
            'MT'  => 'mt_percent',
            'BS'  => 'bs_percent',
            'ST'  => 'st_percent',
        ];

        $totalScore = 0.0;
        $totalEarnedAll = 0.0;
        $correctCount = 0;
        $totalConfiguredPercent = 0.0;

        $earnedByType = [];
        $countByType = [];
        $percentByType = [];

        foreach ($grouped as $type => $qs) {
            $count = $qs->count();
            if ($count === 0) {
                continue;
            }

            $pctField = $typePercentMap[$type] ?? null;
            $percent = $pctField ? (float) ($schedule->bankSoal->$pctField ?? 0) : 0.0;
            $totalConfiguredPercent += $percent;

            $earned = 0.0;
            foreach ($qs as $q) {
                $qScore = self::evaluateQuestionScore($type, $q->cor_ans, $q->complex_options, $q->stu_ans, $q->es_score);
                $earned += $qScore;
                $totalEarnedAll += $qScore;

                if ($qScore >= 0.999 || ($type === 'UR' && $qScore > 0)) {
                    $correctCount++;
                }
            }

            $earnedByType[$type] = $earned;
            $countByType[$type] = $count;
            $percentByType[$type] = $percent;
        }

        if ($totalConfiguredPercent > 0) {
            foreach ($earnedByType as $type => $earned) {
                $count = $countByType[$type];
                $percent = $percentByType[$type];
                if ($percent > 0 && $count > 0) {
                    $totalScore += ($earned / $count) * $percent;
                }
            }
        } else {
            // Fallback otomatis jika guru belum mengatur bobot persen (semua 0%), bagi rata ke 100
            $totalScore = ($totalEarnedAll / $totalQ) * 100.0;
        }

        return [
            'total_questions' => $totalQ,
            'correct_answers' => $correctCount,
            'score' => round($totalScore, 1),
        ];
    }

    public static function calculateScoreAndFinish($scheduleId, $siswaId)
    {
        $metrics = self::computeMetricsForStudent($scheduleId, $siswaId);
        if (!$metrics) {
            return null;
        }

        return self::updateOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswaId],
            [
                'finished_at' => now(),
                'status' => 'finished',
                'total_questions' => $metrics['total_questions'],
                'correct_answers' => $metrics['correct_answers'],
                'score' => $metrics['score'],
            ]
        );
    }

    public static function recalculateExisting($scheduleId, $siswaId)
    {
        $result = self::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->first();

        if (!$result) {
            return null;
        }

        $metrics = self::computeMetricsForStudent($scheduleId, $siswaId);
        if (!$metrics) {
            return $result;
        }

        $result->update([
            'total_questions' => $metrics['total_questions'],
            'correct_answers' => $metrics['correct_answers'],
            'score' => $metrics['score'],
        ]);

        return $result;
    }
}
