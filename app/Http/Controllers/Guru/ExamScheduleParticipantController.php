<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\Group;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ExamScheduleParticipantController extends Controller
{
    private function checkAuthorization(ExamSchedule $schedule)
    {
        $guruSubjectIds = auth('guru')->user()->subjects->pluck('id')->toArray();
        if (!empty($guruSubjectIds) && $schedule->bankSoal && !in_array($schedule->bankSoal->subject_id, $guruSubjectIds)) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola peserta jadwal ujian ini.');
        }
    }

    /**
     * Tampilkan halaman mapping grup ke jadwal dan daftar peserta untuk Guru.
     */
    public function show(Request $request, string $scheduleId)
    {
        $schedule = ExamSchedule::with(['examEvent', 'bankSoal', 'groups'])->findOrFail($scheduleId);
        $this->checkAuthorization($schedule);

        $assignedGroupIds = $schedule->groups->pluck('id')->toArray();

        if ($request->ajax()) {
            $data = Siswa::whereIn('group_id', $assignedGroupIds)
                ->with(['group', 'jurusan'])
                ->select('siswas.*');

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('group_name', fn($row) => $row->group->name ?? '-')
                ->addColumn('jurusan_name', fn($row) => $row->jurusan->name ?? '-')
                ->make(true);
        }

        $allGroups = Group::withCount('siswas')->get();

        return view('guru.exam-schedule.participants', compact('schedule', 'allGroups', 'assignedGroupIds'));
    }

    /**
     * Sync grup peserta ke jadwal ini oleh Guru.
     */
    public function syncGroups(Request $request, string $scheduleId)
    {
        $schedule = ExamSchedule::with('bankSoal')->findOrFail($scheduleId);
        $this->checkAuthorization($schedule);

        $groupIds = $request->input('group_ids', []);
        $schedule->groups()->sync($groupIds);

        return redirect()
            ->route('guru.exam-schedule.participants', $scheduleId)
            ->with('success', 'Mapping peserta berhasil disimpan.');
    }
}

