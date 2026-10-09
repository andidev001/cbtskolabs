<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\ExamResult;
use App\Models\ExamAnswer;
use App\Models\Siswa;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamMonitoringController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExamSchedule::with(['examEvent', 'bankSoal'])
                ->where('is_active', true)
                ->whereDate('start_at', '<=', now()->toDateString())
                ->whereDate('end_at', '>=', now()->toDateString())
                ->select('exam_schedules.*');

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('event_name', function ($row) {
                return $row->examEvent->name ?? '-';
            })
                ->addColumn('bank_soal_name', function ($row) {
                return $row->bankSoal->name ?? '-';
            })
                ->addColumn('action', function ($row) {
                return '<div class="text-center">
                                <a href="' . route('admin.monitoring.show', $row->id) . '" class="btn btn-sm btn-info text-white" title="Monitoring" data-bs-toggle="tooltip"><i class="align-middle" data-feather="monitor"></i></a>
                            </div>';
            })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.monitoring.index');
    }

    public function show($id)
    {
        $schedule = ExamSchedule::with(['bankSoal', 'groups'])->findOrFail($id);

        // Total soal di bank soal ini
        $totalQuestions = Question::where('bank_soal_id', $schedule->bank_soal_id)->count();

        // Ambil semua siswa dari grup yang di-assign
        $assignedGroupIds = $schedule->groups->pluck('id')->toArray();
        $students = Siswa::whereIn('group_id', $assignedGroupIds)
            ->with(['group'])
            ->orderBy('name')
            ->get();

        // Ambil hasil ujian siswa
        $results = ExamResult::where('exam_schedule_id', $id)
            ->get()
            ->keyBy('siswa_id');

        // Ambil jumlah jawaban per siswa
        $answerCounts = ExamAnswer::where('exam_schedule_id', $id)
            ->select('siswa_id', DB::raw('count(*) as total'))
            ->groupBy('siswa_id')
            ->get()
            ->keyBy('siswa_id');

        return view('admin.monitoring.show', compact('schedule', 'students', 'results', 'answerCounts', 'totalQuestions'));
    }

    public function forceFinish($scheduleId, $siswaId)
    {
        $result = ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($result && !$result->finished_at) {
            // Hitung skor saat ini
            $answers = ExamAnswer::where('exam_schedule_id', $scheduleId)
                ->where('siswa_id', $siswaId)
                ->with('question')
                ->get();

            $total = Question::where('bank_soal_id', $result->examSchedule->bank_soal_id)->count();
            $correct = $answers->filter(fn($a) => strtoupper($a->answer ?? '') === strtoupper($a->question->answer ?? ''))->count();
            $score = $total > 0 ? round($correct / $total * 100, 1) : 0;

            $result->update([
                'finished_at' => now(),
                'total_questions' => $total,
                'correct_answers' => $correct,
                'score' => $score
            ]);

            return redirect()->back()->with('success', 'Siswa berhasil dipaksa selesai.');
        }

        return redirect()->back()->with('error', 'Siswa sudah selesai atau belum memulai.');
    }

    public function resetExam($scheduleId, $siswaId)
    {
        // Hapus hasil dan jawaban untuk reset
        ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->delete();

        ExamAnswer::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->delete();

        return redirect()->back()->with('success', 'Ujian siswa berhasil direset.');
    }

    public function unblock($scheduleId, $siswaId)
    {
        $result = ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($result && $result->status === 'blocked') {
            $result->update([
                'status' => 'ongoing',
                'finished_at' => null
            ]);
            return redirect()->back()->with('success', 'Blokir ujian berhasil dibuka. Siswa kini bisa melanjutkan ujian.');
        }

        return redirect()->back()->with('error', 'Status siswa bukan terblokir.');
    }
}
