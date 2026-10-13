<?php

namespace App\Http\Controllers\Guru;

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
    private function authorizedSchedule(ExamSchedule $examSchedule)
    {
        $guruSubjectIds = auth('guru')->user()->subjects->pluck('id')->toArray();
        if (!in_array($examSchedule->bankSoal->subject_id, $guruSubjectIds)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $guruSubjectIds = auth('guru')->user()->subjects->pluck('id')->toArray();

            $data = ExamSchedule::with(['examEvent', 'bankSoal'])
                ->whereHas('bankSoal', function ($q) use ($guruSubjectIds) {
                    $q->whereIn('subject_id', $guruSubjectIds);
                })
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
                                <a href="' . route('guru.monitoring.show', $row->id) . '" class="btn btn-sm btn-info text-white" title="Monitoring" data-bs-toggle="tooltip"><i class="align-middle" data-feather="monitor"></i></a>
                            </div>';
            })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('guru.monitoring.index');
    }

        public function show(Request $request, $id)
    {
        $schedule = ExamSchedule::with(['bankSoal', 'groups'])->findOrFail($id);
        $this->authorizedSchedule($schedule);
        
        $totalQuestions = Question::where('bank_soal_id', $schedule->bank_soal_id)->count();
        $assignedGroupIds = $schedule->groups->pluck('id')->toArray();

        if ($request->ajax()) {
            $query = Siswa::whereIn('group_id', $assignedGroupIds)->with(['group']);
            
            if ($request->has('group_id') && $request->group_id != '') {
                $query->where('group_id', $request->group_id);
            }

            $students = $query->orderBy('name')->get();
            $results = ExamResult::where('exam_schedule_id', $id)->get()->keyBy('siswa_id');
            
            // Auto-finish if time is up
            $now = \Carbon\Carbon::now();
            foreach ($students as $student) {
                $res = $results->get($student->id);
                if ($res && !$res->finished_at && $res->status !== 'blocked') {
                    $examEnd = \Carbon\Carbon::parse($schedule->end_at);
                    
                    $session = \App\Models\ExamSession::where('name', $student->sesi)->first();
                    if ($session) {
                        $sessionEnd = \Carbon\Carbon::createFromFormat('H:i:s', $session->end_time);
                        if ($sessionEnd->lt($examEnd)) {
                            $examEnd = $sessionEnd;
                        }
                    }
                    
                    $maxEnd = $res->started_at->copy()->addMinutes($schedule->duration);
                    if ($maxEnd->lt($examEnd)) {
                        $examEnd = $maxEnd;
                    }
                    
                    if ($now->gt($examEnd)) {
                        $newRes = \App\Models\ExamResult::calculateScoreAndFinish($schedule->id, $student->id);
                        $results->put($student->id, $newRes);
                    }
                }
            }

            $answerCounts = ExamAnswer::where('exam_schedule_id', $id)
                ->select('siswa_id', DB::raw('count(*) as total'))
                ->groupBy('siswa_id')
                ->get()
                ->keyBy('siswa_id');

            // Hitung statistik untuk ringkasan & kartu filter
            $totalCount = $students->count();
            $blockedCount = 0;
            $ongoingCount = 0;
            $finishedCount = 0;
            $notStartedCount = 0;

            foreach ($students as $student) {
                $res = $results->get($student->id);
                if (!$res) {
                    $notStartedCount++;
                } elseif ($res->status === 'blocked') {
                    $blockedCount++;
                } elseif ($res->status === 'finished' || $res->finished_at) {
                    $finishedCount++;
                } else {
                    $ongoingCount++;
                }
            }

            // Filter status jika dipilih
            if ($request->has('status_filter') && $request->status_filter != '') {
                $sf = $request->status_filter;
                $students = $students->filter(function($student) use ($results, $sf) {
                    $res = $results->get($student->id);
                    if ($sf === 'blocked') return $res && $res->status === 'blocked';
                    if ($sf === 'ongoing') return $res && $res->status !== 'blocked' && !$res->finished_at && $res->status !== 'finished';
                    if ($sf === 'finished') return $res && ($res->status === 'finished' || $res->finished_at);
                    if ($sf === 'not_started') return !$res;
                    return true;
                })->values();
            }

            return \Yajra\DataTables\Facades\DataTables::of($students)
                ->addIndexColumn()
                ->with([
                    'totalCount' => $totalCount,
                    'blockedCount' => $blockedCount,
                    'ongoingCount' => $ongoingCount,
                    'finishedCount' => $finishedCount,
                    'notStartedCount' => $notStartedCount,
                ])
                ->addColumn('nomer_ujian', function($row) {
                    return $row->nomer_ujian;
                })
                ->addColumn('name', function($row) {
                    return $row->name;
                })
                ->addColumn('group_name', function($row) {
                    return $row->group->name ?? '-';
                })
                ->addColumn('status', function($row) use ($results) {
                    $result = $results->get($row->id);
                    if (!$result) {
                        return '<span class="badge bg-secondary">Belum Mulai</span>';
                    }
                    if ($result->status === 'blocked') {
                        $reason = !empty($result->block_reason) ? htmlspecialchars($result->block_reason) : 'Membuka tab/aplikasi lain';
                        return '<span class="badge bg-danger"><i data-feather="alert-triangle" style="width: 12px; height: 12px;" class="align-middle me-1"></i> Diblokir</span>' .
                               '<div class="text-danger fw-bold mt-1" style="font-size: 0.72rem; line-height: 1.2;">' . $reason . '</div>';
                    }
                    if ($result->status === 'finished' || $result->finished_at) {
                        return '<span class="badge bg-success">Selesai</span>';
                    }
                    $html = '<span class="badge bg-primary">Sedang Mengerjakan</span>';
                    if ($result->started_at) {
                        $html .= '<div class="small text-muted mt-1" style="font-size: 0.7rem;">Mulai: ' . $result->started_at->format('H:i') . '</div>';
                    }
                    return $html;
                })
                ->addColumn('progress', function($row) use ($results, $answerCounts, $totalQuestions) {
                    $answerCount = $answerCounts->get($row->id)->total ?? 0;
                    $progress = $totalQuestions > 0 ? round(($answerCount / $totalQuestions) * 100) : 0;
                    $html = '<div class="d-flex align-items-center gap-2">';
                    $html .= '<div class="progress flex-grow-1" style="height: 6px;"><div class="progress-bar" role="progressbar" style="width: '.$progress.'%;" aria-valuenow="'.$progress.'" aria-valuemin="0" aria-valuemax="100"></div></div>';
                    $html .= '<span class="small fw-bold">'.$answerCount.'/'.$totalQuestions.'</span>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('score', function($row) use ($results) {
                    $result = $results->get($row->id);
                    if ($result && $result->finished_at) {
                        return '<span class="text-success fw-bold">' . $result->score . '</span>';
                    }
                    return '<span class="text-muted fw-bold">-</span>';
                })
                ->addColumn('action', function($row) use ($results, $schedule) {
                    $result = $results->get($row->id);
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    if ($result && !$result->finished_at && $result->status !== 'blocked') {
                        $url = route('guru.monitoring.force-finish', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST" class="form-force-finish">'.csrf_field().'<button type="button" class="btn btn-sm btn-warning text-white py-0 px-2 btn-force-finish" title="Paksa Selesai"><i data-feather="terminal" style="width: 12px; height: 12px;"></i> Selesai</button></form>';
                    }
                    if ($result && $result->status === 'blocked') {
                        $url = route('guru.monitoring.unblock', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST" class="form-unblock">'.csrf_field().'<button type="button" class="btn btn-sm btn-success py-0 px-2 btn-unblock" title="Buka Blokir"><i data-feather="unlock" style="width: 12px; height: 12px;"></i> Buka Blokir</button></form>';
                    }
                    if ($result) {
                        $url = route('guru.monitoring.reset', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST" class="form-reset">'.csrf_field().'<button type="button" class="btn btn-sm btn-danger py-0 px-2 btn-reset" title="Reset Total Ujian"><i data-feather="rotate-ccw" style="width: 12px; height: 12px;"></i> Reset Total</button></form>';
                    }
                    
                    $resetLoginUrl = route('guru.monitoring.reset-login', $row->id);
                    $btn .= '<form action="'.$resetLoginUrl.'" method="POST" class="form-reset-login d-inline">'.csrf_field().'<button type="button" class="btn btn-sm btn-dark py-0 px-2 btn-reset-login" title="Reset Login"><i data-feather="refresh-cw" style="width: 12px; height: 12px;"></i> Reset Login</button></form>';

                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'progress', 'score', 'action'])
                ->make(true);
        }
        
        $groups = \App\Models\Group::whereIn('id', $assignedGroupIds)->orderBy('name')->get();

        return view('guru.monitoring.show', compact('schedule', 'groups', 'totalQuestions'));
    }

    public function forceFinish($scheduleId, $siswaId)
    {
        $schedule = ExamSchedule::findOrFail($scheduleId);
        $this->authorizedSchedule($schedule);

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
        $schedule = ExamSchedule::findOrFail($scheduleId);
        $this->authorizedSchedule($schedule);

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
        $schedule = ExamSchedule::findOrFail($scheduleId);
        $this->authorizedSchedule($schedule);

        $result = ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($result && $result->status === 'blocked') {
            $resetData = [
                'status' => 'ongoing',
                'finished_at' => null,
            ];
            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('exam_results', 'block_reason')) {
                    $resetData['block_reason'] = null;
                }
            } catch (\Throwable $e) {}

            $result->update($resetData);
            $siswa = \App\Models\Siswa::find($siswaId);
            if ($siswa) {
                $siswa->update(['is_logged_in' => false, 'session_id' => null]);
            }
            return redirect()->back()->with('success', 'Blokir ujian berhasil dibuka. Siswa kini bisa login dan melanjutkan ujian.');
        }

        return redirect()->back()->with('error', 'Status siswa bukan terblokir.');
    }

    public function resetLogin($siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);
        $siswa->update([
            'is_logged_in' => false,
            'session_id' => null
        ]);

        $resetData = ['status' => 'ongoing', 'finished_at' => null];
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('exam_results', 'block_reason')) {
                $resetData['block_reason'] = null;
            }
        } catch (\Throwable $e) {}

        \App\Models\ExamResult::where('siswa_id', $siswaId)->where('status', 'blocked')->update($resetData);
        return redirect()->back()->with('success', 'Status login dan blokir ujian siswa berhasil direset.');
    }
}
