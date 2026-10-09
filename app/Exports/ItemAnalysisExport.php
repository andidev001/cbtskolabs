<?php

namespace App\Exports;

use App\Models\ExamSchedule;
use App\Models\ExamResult;
use App\Models\ExamAnswer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ItemAnalysisExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $scheduleId;
    protected $groupId;
    protected $rowNumber = 0;

    public function __construct($scheduleId, $groupId = null)
    {
        $this->scheduleId = $scheduleId;
        $this->groupId = $groupId;
    }

    public function collection()
    {
        $schedule = ExamSchedule::with('bankSoal.questions')->findOrFail($this->scheduleId);
        $questions = $schedule->bankSoal->questions;
        
        $resultQuery = ExamResult::where('exam_schedule_id', $this->scheduleId);
        if ($this->groupId) {
            $resultQuery->whereHas('siswa', function ($q) {
                $q->where('group_id', $this->groupId);
            });
        }
        $results = $resultQuery->orderBy('score', 'desc')->get();
        $totalParticipants = $results->count();

        // Calculate Upper and Lower Group sizes for Discrimination Index (DP)
        $groupSize = $totalParticipants < 10 ? (int) floor($totalParticipants / 2) : (int) round($totalParticipants * 0.27);
        $upperSiswaIds = $results->take($groupSize)->pluck('siswa_id')->toArray();
        $lowerSiswaIds = $results->reverse()->take($groupSize)->pluck('siswa_id')->toArray();

        $answersQuery = ExamAnswer::where('exam_schedule_id', $this->scheduleId);
        if ($this->groupId) {
            $answersQuery->whereHas('siswa', function ($sq) {
                $sq->where('group_id', $this->groupId);
            });
        }
        $allAnswers = $answersQuery->get()->groupBy('question_id');

        $data = collect();
        foreach ($questions as $q) {
            $ansForQ = $allAnswers->get($q->id) ?? collect();
            
            $correctCount = 0;
            $upperCorrect = 0;
            $lowerCorrect = 0;

            foreach ($ansForQ as $a) {
                $isCorrect = (strtoupper($a->answer) === strtoupper($q->answer) || $a->score > 0);
                if ($isCorrect) {
                    $correctCount++;
                    if (in_array($a->siswa_id, $upperSiswaIds)) {
                        $upperCorrect++;
                    } elseif (in_array($a->siswa_id, $lowerSiswaIds)) {
                        $lowerCorrect++;
                    }
                }
            }

            $difficultyRatio = $totalParticipants > 0 ? $correctCount / $totalParticipants : 0;

            $difficultyLevel = 'Sedang';
            if ($difficultyRatio < 0.3)
                $difficultyLevel = 'Sukar';
            elseif ($difficultyRatio > 0.7)
                $difficultyLevel = 'Mudah';
                
            $dp = 0;
            if ($groupSize > 0) {
                $dp = ($upperCorrect - $lowerCorrect) / $groupSize;
            }
            
            $dpCategory = 'Sangat Jelek';
            if ($dp > 0.40) $dpCategory = 'Sangat Baik';
            elseif ($dp >= 0.30) $dpCategory = 'Baik';
            elseif ($dp >= 0.20) $dpCategory = 'Cukup';
            elseif ($dp > 0) $dpCategory = 'Jelek';

            $data->push([
                'id' => $q->id,
                'question_text' => strip_tags($q->question_text),
                'type' => $q->type,
                'cognitive_level' => $q->cognitive_level ?? '-',
                'correct_count' => $correctCount,
                'total_participants' => $totalParticipants,
                'difficulty_ratio' => round($difficultyRatio, 2),
                'difficulty_level' => $difficultyLevel,
                'discrimination_index' => round($dp, 2),
                'discrimination_category' => $dpCategory,
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Pertanyaan',
            'Tipe',
            'Level Kognitif',
            'Jumlah Benar',
            'Total Peserta',
            'Indeks Kesukaran',
            'Tingkat Kesukaran',
            'Daya Pembeda',
            'Status DP'
        ];
    }

    public function map($item): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $item['question_text'],
            $item['type'],
            $item['cognitive_level'],
            $item['correct_count'],
            $item['total_participants'],
            $item['difficulty_ratio'],
            $item['difficulty_level'],
            $item['discrimination_index'],
            $item['discrimination_category']
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
