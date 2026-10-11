<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\BankSoal;
use App\Models\ExamResult;
use App\Models\ExamAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ProcessingController extends Controller
{
    private function mySubjectIds(): array
    {
        return Auth::guard('guru')->user()
            ->subjects()->pluck('subjects.id')->toArray();
    }

    private function myBankSoalIds(): array
    {
        return BankSoal::whereIn('subject_id', $this->mySubjectIds())
            ->pluck('id')->toArray();
    }

    private function mySchedules()
    {
        return ExamSchedule::with(['examEvent', 'bankSoal'])
            ->whereIn('bank_soal_id', $this->myBankSoalIds());
    }

    /** ── Koreksi Esai ── */
    public function koreksiIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->mySchedules()
                ->whereHas('bankSoal.questions', fn($q) => $q->where('type', 'Essay'))
                ->select('exam_schedules.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('event_name', fn($row) => $row->examEvent->name ?? '-')
                ->addColumn('bank_soal_name', fn($row) => $row->bankSoal->name ?? '-')
                ->addColumn('action', fn($row) =>
            '<a href="' . route('guru.processing.koreksi.show', $row->id) . '" class="btn btn-sm btn-primary">
                        <i data-feather="check-square"></i> Mulai Koreksi
                    </a>'
            )
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('guru.processing.koreksi-index');
    }

    public function koreksiShow(int $scheduleId)
    {
        $schedule = $this->mySchedules()->with(['bankSoal.questions'])->findOrFail($scheduleId);
        $participants = ExamResult::where('exam_schedule_id', $scheduleId)
            ->with(['siswa.group'])
            ->get();
        return view('guru.processing.koreksi-show', compact('schedule', 'participants'));
    }

    public function koreksiSiswa($scheduleId, $siswaId)
    {
        $schedule = $this->mySchedules()->with(['bankSoal'])->findOrFail($scheduleId);
        $siswa = \App\Models\Siswa::findOrFail($siswaId);
        $result = ExamResult::where('exam_schedule_id', $scheduleId)->where('siswa_id', $siswaId)->firstOrFail();
        
        $questions = \App\Models\Question::where('bank_soal_id', $schedule->bank_soal_id)
            ->whereIn('type', ['Essay', 'UR'])
            ->get();
            
        $answers = ExamAnswer::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        return view('guru.processing.koreksi-siswa', compact('schedule', 'siswa', 'result', 'questions', 'answers'));
    }

    public function koreksiStore(Request $request, $scheduleId, $siswaId)
    {
        $request->validate([
            'scores' => 'required|array',
            'scores.*' => 'numeric|min:0|max:100',
        ]);

        foreach ($request->scores as $questionId => $score) {
            ExamAnswer::updateOrCreate(
                [
                    'exam_schedule_id' => $scheduleId,
                    'siswa_id' => $siswaId,
                    'question_id' => $questionId
                ],
                [
                    'score' => $score
                ]
            );
        }

        $this->recalculateScore($scheduleId, $siswaId);

        return redirect()->route('guru.processing.koreksi.show', $scheduleId)->with('success', 'Nilai esai berhasil disimpan.');
    }

    private function recalculateScore($scheduleId, $siswaId)
    {
        $schedule = ExamSchedule::with('bankSoal')->findOrFail($scheduleId);
        $result = ExamResult::where('exam_schedule_id', $scheduleId)->where('siswa_id', $siswaId)->first();
        if (!$result) return;

        $questions = \App\Models\Question::where('bank_soal_id', $schedule->bank_soal_id)
            ->leftJoin('exam_answers', function ($j) use ($scheduleId, $siswaId) {
                $j->on('questions.id', '=', 'exam_answers.question_id')
                  ->where('exam_answers.exam_schedule_id', $scheduleId)
                  ->where('exam_answers.siswa_id', $siswaId);
            })->select('questions.id', 'questions.type', 'questions.answer as cor_ans', 'questions.complex_options', 'exam_answers.answer as stu_ans', 'exam_answers.score as es_score')->get();

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
                    $earned += ((float) ($q->es_score ?: 0)) / 100;
                }
            }
            
            if ($percent > 0) {
                $totalScore += ($earned / $count) * $percent;
            }
        }

        $result->update([
            'total_questions' => $totalQ,
            'correct_answers' => $correctPG,
            'score' => round($totalScore, 1)
        ]);
    }

    /** ── Hasil Ujian ── */
    public function hasilIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->mySchedules()->select('exam_schedules.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('event_name', fn($row) => $row->examEvent->name ?? '-')
                ->addColumn('bank_soal_name', fn($row) => $row->bankSoal->name ?? '-')
                ->addColumn('peserta', fn($row) => ExamResult::where('exam_schedule_id', $row->id)->count())
                ->addColumn('action', fn($row) =>
            '<a href="' . route('guru.processing.hasil.show', $row->id) . '" class="btn btn-sm btn-info text-white">
                        <i data-feather="bar-chart-2"></i> Lihat Nilai
                    </a>'
            )
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('guru.processing.hasil-index');
    }

    public function hasilShow(int $scheduleId)
    {
        $schedule = $this->mySchedules()
            ->with(['examEvent', 'bankSoal'])
            ->findOrFail($scheduleId);

        $results = ExamResult::with(['siswa.group'])
            ->where('exam_schedule_id', $scheduleId)
            ->orderByDesc('score')
            ->get();

        return view('guru.processing.hasil-show', compact('schedule', 'results'));
    }

    public function hasilExportExcel(Request $request, $scheduleId)
    {
        $schedule = $this->mySchedules()->findOrFail($scheduleId);
        $fileName = 'Hasil_Ujian_' . str_replace(' ', '_', $schedule->name) . '_' . date('YmdHis') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExamResultExport($scheduleId, $request->group_id), $fileName);
    }

    public function hasilRecalculateAll($scheduleId)
    {
        $schedule = $this->mySchedules()->findOrFail($scheduleId);
        $results = ExamResult::where('exam_schedule_id', $schedule->id)->whereNotNull('finished_at')->get();
        foreach ($results as $res) {
            \App\Jobs\CalculateExamScoreJob::dispatch($schedule->id, $res->siswa_id);
        }

        return back()->with('success', 'Kalkulasi ulang nilai untuk ' . $results->count() . ' siswa berhasil dimasukkan ke antrean background.');
    }

    /** ── Leger Nilai ── */
    public function legerIndex(Request $request)
    {
        $groups = \App\Models\Group::all();
        $examEvents = \App\Models\ExamEvent::all();

        $siswa = collect();
        $schedules = collect();
        $results = collect();
        $selectedGroup = null;
        $selectedEvent = null;

        if ($request->has('group_id') && $request->has('exam_event_id')) {
            $groupId = $request->group_id;
            $eventId = $request->exam_event_id;

            $selectedGroup = \App\Models\Group::find($groupId);
            $selectedEvent = \App\Models\ExamEvent::find($eventId);

            if ($selectedGroup && $selectedEvent) {
                // Siswa dalam grup ini
                $siswa = \App\Models\Siswa::where('group_id', $groupId)->orderBy('name')->get();

                // Ambil SEMUA jadwal ujian (seluruh mata pelajaran) dalam event ini untuk grup ini
                $schedules = \App\Models\ExamSchedule::with('bankSoal')->where('exam_event_id', $eventId)
                    ->whereHas('groups', function ($q) use ($groupId) {
                        $q->where('groups.id', $groupId);
                    })
                    ->get();

                // Ambil semua hasil ujian untuk siswa dan jadwal terpilih
                $results = ExamResult::whereIn('exam_schedule_id', $schedules->pluck('id'))
                    ->whereIn('siswa_id', $siswa->pluck('id'))
                    ->get()
                    ->groupBy('siswa_id');
            }
        }

        return view('admin.processing.leger-index', compact(
            'groups', 'examEvents', 'siswa', 'schedules', 'results', 'selectedGroup', 'selectedEvent'
        ));
    }

    public function legerExportExcel(Request $request)
    {
        $groupId = $request->group_id;
        $eventId = $request->exam_event_id;
        
        $selectedGroup = \App\Models\Group::findOrFail($groupId);
        $fileName = 'Leger_Nilai_' . str_replace(' ', '_', $selectedGroup->name) . '_' . date('YmdHis') . '.xlsx';
        
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\LegerExport($groupId, $eventId), $fileName);
    }

    public function legerExportPdf(Request $request)
    {
        $groupId = $request->group_id;
        $eventId = $request->exam_event_id;

        $selectedGroup = \App\Models\Group::findOrFail($groupId);
        $selectedEvent = \App\Models\ExamEvent::findOrFail($eventId);

        $siswa = \App\Models\Siswa::where('group_id', $groupId)->orderBy('name')->get();

        $schedules = \App\Models\ExamSchedule::with('bankSoal')->where('exam_event_id', $eventId)
            ->whereHas('groups', function ($q) use ($groupId) {
                $q->where('groups.id', $groupId);
            })
            ->get();

        $results = ExamResult::whereIn('exam_schedule_id', $schedules->pluck('id'))
            ->whereIn('siswa_id', $siswa->pluck('id'))
            ->get()
            ->groupBy('siswa_id');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.processing.leger-export', [
            'selectedGroup' => $selectedGroup,
            'selectedEvent' => $selectedEvent,
            'siswa' => $siswa,
            'schedules' => $schedules,
            'results' => $results,
            'isPdf' => true
        ]);
        
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'Leger_Nilai_' . str_replace(' ', '_', $selectedGroup->name) . '_' . date('YmdHis') . '.pdf';
        
        return $pdf->download($fileName);
    }

    /** ── Analisis Butir Soal ── */
    public function analisisIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->mySchedules()->select('exam_schedules.*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('event_name', fn($row) => $row->examEvent->name ?? '-')
                ->addColumn('bank_soal_name', fn($row) => $row->bankSoal->name ?? '-')
                ->addColumn('action', function ($row) {
                    return '<div class="text-center">
                                <a href="' . route('guru.processing.analisis.show', $row->id) . '" class="btn btn-sm btn-success">
                                    <i class="align-middle" data-feather="bar-chart-2"></i> Analisis
                                </a>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.processing.analisis-index'); // We can reuse admin views if they don't depend on admin layouts strictly, or we should change their layout dynamically. Wait, admin views extend 'layouts.admin'.
    }

    public function analisisShow(Request $request, $scheduleId)
    {
        $schedule = $this->mySchedules()->with(['bankSoal.questions', 'examEvent'])->findOrFail($scheduleId);

        if ($request->ajax()) {
            $groupId = $request->input('group_id');
            $questions = $schedule->bankSoal->questions;

            // Base query for participants
            $resultQuery = ExamResult::where('exam_schedule_id', $scheduleId);
            if ($groupId) {
                $resultQuery->whereHas('siswa', function ($q) use ($groupId) {
                    $q->where('group_id', $groupId);
                });
            }
            $results = $resultQuery->orderBy('score', 'desc')->get();
            $totalParticipants = $results->count();

            // Calculate Upper and Lower Group sizes for Discrimination Index (DP)
            $groupSize = $totalParticipants < 10 ? (int) floor($totalParticipants / 2) : (int) round($totalParticipants * 0.27);
            $upperSiswaIds = $results->take($groupSize)->pluck('siswa_id')->toArray();
            $lowerSiswaIds = $results->reverse()->take($groupSize)->pluck('siswa_id')->toArray();

            // Fetch answers optimized
            $answersQuery = ExamAnswer::where('exam_schedule_id', $scheduleId);
            if ($groupId) {
                $answersQuery->whereHas('siswa', function ($sq) use ($groupId) {
                    $sq->where('group_id', $groupId);
                });
            }
            $allAnswers = $answersQuery->get()->groupBy('question_id');

            $correctCounts = [];
            $dpData = [];
            
            foreach ($questions as $q) {
                $ansForQ = $allAnswers->get($q->id) ?? collect();
                
                $c = 0;
                $upperCorrect = 0;
                $lowerCorrect = 0;

                foreach ($ansForQ as $a) {
                    $isCorrect = (strtoupper($a->answer) === strtoupper($q->answer) || $a->score > 0);
                    if ($isCorrect) {
                        $c++;
                        if (in_array($a->siswa_id, $upperSiswaIds)) {
                            $upperCorrect++;
                        } elseif (in_array($a->siswa_id, $lowerSiswaIds)) {
                            $lowerCorrect++;
                        }
                    }
                }
                
                $correctCounts[$q->id] = $c;

                // Calculate DP
                $dp = 0;
                if ($groupSize > 0) {
                    $dp = ($upperCorrect - $lowerCorrect) / $groupSize;
                }
                $dpData[$q->id] = round($dp, 2);
            }

            return \Yajra\DataTables\Facades\DataTables::of($questions)
                ->addIndexColumn()
                ->addColumn('cuplikan', function ($q) {
                    return \Illuminate\Support\Str::limit(strip_tags($q->question_text), 100);
                })
                ->addColumn('cognitive_level', function ($q) {
                    return $q->cognitive_level ?? '-';
                })
                ->addColumn('correct_count', function ($q) use ($correctCounts) {
                    return $correctCounts[$q->id] ?? 0;
                })
                ->addColumn('total_participants', function ($q) use ($totalParticipants) {
                    return $totalParticipants;
                })
                ->addColumn('difficulty_ratio', function ($q) use ($correctCounts, $totalParticipants) {
                    $c = $correctCounts[$q->id] ?? 0;
                    return $totalParticipants > 0 ? round($c / $totalParticipants, 2) : 0;
                })
                ->addColumn('difficulty_level', function ($q) use ($correctCounts, $totalParticipants) {
                    $c = $correctCounts[$q->id] ?? 0;
                    $ratio = $totalParticipants > 0 ? $c / $totalParticipants : 0;

                    if ($ratio < 0.3) return '<span class="badge bg-danger">Sukar</span>';
                    if ($ratio > 0.7) return '<span class="badge bg-success">Mudah</span>';
                    return '<span class="badge bg-warning">Sedang</span>';
                })
                ->addColumn('discrimination_index', function ($q) use ($dpData) {
                    return $dpData[$q->id] ?? 0;
                })
                ->addColumn('discrimination_category', function ($q) use ($dpData) {
                    $dp = $dpData[$q->id] ?? 0;
                    if ($dp > 0.40) return '<span class="badge bg-success">Sangat Baik</span>';
                    if ($dp >= 0.30) return '<span class="badge bg-primary">Baik</span>';
                    if ($dp >= 0.20) return '<span class="badge bg-warning">Cukup</span>';
                    if ($dp > 0) return '<span class="badge bg-danger">Jelek</span>';
                    return '<span class="badge bg-dark">Sangat Jelek</span>';
                })
                ->rawColumns(['difficulty_level', 'discrimination_category'])
                ->make(true);
        }

        $groupIds = ExamResult::where('exam_schedule_id', $scheduleId)
            ->join('siswas', 'siswas.id', '=', 'exam_results.siswa_id')
            ->pluck('siswas.group_id')
            ->unique();
        $groups = \App\Models\Group::whereIn('id', $groupIds)->get();

        return view('admin.processing.analisis-show', compact('schedule', 'groups'));
    }

    public function analisisExportExcel(Request $request, $scheduleId)
    {
        $schedule = $this->mySchedules()->findOrFail($scheduleId);
        $groupId = $request->input('group_id');
        $groupName = '';
        if ($groupId) {
            $groupName = '_' . \App\Models\Group::find($groupId)->name;
        }

        $fileName = 'Analisis_Butir_Soal_' . str_replace(' ', '_', $schedule->name) . $groupName . '_' . date('YmdHis') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ItemAnalysisExport($scheduleId, $groupId), $fileName);
    }
}
