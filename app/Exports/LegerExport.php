<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\Group;
use App\Models\Siswa;
use App\Models\ExamSchedule;
use App\Models\ExamResult;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegerExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $groupId;
    protected $eventId;

    public function __construct($groupId, $eventId)
    {
        $this->groupId = $groupId;
        $this->eventId = $eventId;
    }

    public function view(): View
    {
        $selectedGroup = Group::find($this->groupId);
        $selectedEvent = \App\Models\ExamEvent::find($this->eventId);

        $siswa = collect();
        $schedules = collect();
        $results = collect();

        if ($selectedGroup && $selectedEvent) {
            $siswa = Siswa::where('group_id', $this->groupId)->orderBy('name')->get();

            $schedules = ExamSchedule::where('exam_event_id', $this->eventId)
                ->whereHas('groups', function ($q) {
                    $q->where('groups.id', $this->groupId);
                })
                ->with('bankSoal')
                ->get();

            $results = ExamResult::whereIn('exam_schedule_id', $schedules->pluck('id'))
                ->whereIn('siswa_id', $siswa->pluck('id'))
                ->get()
                ->groupBy('siswa_id');
        }

        return view('admin.processing.leger-export', [
            'selectedGroup' => $selectedGroup,
            'selectedEvent' => $selectedEvent,
            'siswa' => $siswa,
            'schedules' => $schedules,
            'results' => $results,
            'isPdf' => false
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
            2    => ['font' => ['bold' => true]],
        ];
    }
}
