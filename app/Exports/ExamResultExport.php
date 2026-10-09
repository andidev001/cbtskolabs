<?php

namespace App\Exports;

use App\Models\ExamResult;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExamResultExport implements FromQuery, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $scheduleId;
    protected $groupId;
    protected $rowNumber = 0;

    public function __construct($scheduleId, $groupId = null)
    {
        $this->scheduleId = $scheduleId;
        $this->groupId = $groupId;
    }

    public function query()
    {
        $query = ExamResult::where('exam_schedule_id', $this->scheduleId)
            ->with(['siswa.group'])
            ->orderBy('score', 'desc');
            
        if ($this->groupId) {
            $query->whereHas('siswa', function($q) {
                $q->where('group_id', $this->groupId);
            });
        }
        
        return $query;
    }

    public function map($result): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $result->siswa->nomer_ujian,
            $result->siswa->name,
            $result->siswa->group->name ?? '-',
            $result->correct_answers,
            $result->total_questions - $result->correct_answers,
            $result->score,
            $result->finished_at ? $result->finished_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Peserta',
            'Nama Siswa',
            'Grup/Kelas',
            'Benar',
            'Salah',
            'Skor',
            'Waktu Selesai',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
