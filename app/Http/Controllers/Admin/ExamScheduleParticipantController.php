<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\Group;
use App\Models\Siswa;
use Illuminate\Http\Request;

class ExamScheduleParticipantController extends Controller
{
    /**
     * Tampilkan halaman mapping grup ke jadwal dan daftar peserta.
     */
    public function show(Request $request, string $scheduleId)
    {
        $schedule = ExamSchedule::with(['examEvent', 'bankSoal', 'groups'])->findOrFail($scheduleId);
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

        return view('admin.exam-schedule.participants', compact('schedule', 'allGroups', 'assignedGroupIds'));
    }

    /**
     * Sync grup peserta ke jadwal ini.
     */
    public function syncGroups(Request $request, string $scheduleId)
    {
        $schedule = ExamSchedule::findOrFail($scheduleId);

        $groupIds = $request->input('group_ids', []);
        $schedule->groups()->sync($groupIds);

        return redirect()
            ->route('admin.exam-schedule.participants', $scheduleId)
            ->with('success', 'Mapping peserta berhasil disimpan.');
    }
}
