<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ExamAnswer;
use App\Models\ExamResult;
use App\Models\ExamSchedule;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ExamController extends Controller
{
    /** Mulai ujian – load soal & redirect ke soal pertama */
    public function start(Request $request, $scheduleId)
    {
        $siswa = Auth::guard('siswa')->user();
        $schedule = ExamSchedule::with('bankSoal.subject')->findOrFail($scheduleId);
        $schedule->maybeRefreshToken();

        $now = Carbon::now();

        // Validasi waktu dan status
        abort_if(!$schedule->is_active, 403, 'Ujian tidak aktif.');
        abort_if($now->lt($schedule->start_at), 403, 'Ujian belum dimulai.');
        abort_if($now->gt($schedule->end_at), 403, 'Waktu ujian telah berakhir.');

        // Validasi Agama
        $subject = $schedule->bankSoal->subject;
        abort_if($subject && $subject->religion_id !== null && $subject->religion_id != $siswa->religion_id, 403, 'Ujian ini tidak tersedia untuk Anda (Beda Agama).');

        // Validasi Sesi Siswa
        $siswaSession = \App\Models\ExamSession::where('name', $siswa->sesi)->first();
        if ($siswaSession) {
            $curTime = $now->format('H:i:s');
            abort_if($curTime < $siswaSession->start_time, 403, 'Sesi Anda belum dimulai.');
            abort_if($curTime > $siswaSession->end_time, 403, 'Waktu sesi Anda telah berakhir.');
        }

        // Validasi Token (Server Side)
        if ($schedule->token) {
            if (!$request->token || strtoupper($request->token) !== strtoupper($schedule->token)) {
                return redirect()->route('siswa.dashboard')->with('error', 'Token ujian tidak valid atau belum dirilis.');
            }
        }

        // Pastikan siswa ada di grup yang di-assign ke jadwal ini (Cache 60s untuk hindari ribuan join DB)
        $allowedGroupIds = Cache::remember("sched_groups_{$scheduleId}", 60, function () use ($schedule) {
            return $schedule->groups()->pluck('groups.id')->toArray();
        });
        abort_if(!in_array($siswa->group_id, $allowedGroupIds), 403, 'Anda tidak terdaftar dalam ujian ini.');

        // Cek apakah sudah pernah mengerjakan (hanya boleh 1 kali)
        $existingResult = ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswa->id)
            ->first();
            
        if ($existingResult) {
            if ($existingResult->status === 'blocked') {
                return redirect()->route('siswa.dashboard')->with('error', 'Ujian Anda telah diblokir.');
            }
            if ($existingResult->finished_at) {
                return redirect()->route('siswa.dashboard')->with('error', 'Anda sudah menyelesaikan ujian ini.');
            }
        }

        // Catat waktu mulai di database jika belum ada, JANGAN update jika sudah ada
        $result = ExamResult::firstOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswa->id],
            ['started_at' => $now, 'status' => 'ongoing']
        );

        // Simpan/restore waktu mulai ujian ke session (untuk hitung sisa waktu)
        $sessionKey = "exam_start_{$scheduleId}_{$siswa->id}";
        if (!session()->has($sessionKey)) {
            session([$sessionKey => clone $result->started_at]);
        }

        return redirect()->route('siswa.exam.show', ['schedule' => $scheduleId, 'no' => 1]);
    }

    /** Tampilkan soal ke-N */
    public function show(Request $request, $scheduleId, $no = 1)
    {
        $siswa = Auth::guard('siswa')->user();
        
        // Cache metadata jadwal ujian selama 60 detik (mengurangi 98% beban query schedule)
        $schedule = Cache::remember("exam_sched_meta_{$scheduleId}", 60, function () use ($scheduleId) {
            return ExamSchedule::select('id', 'name', 'exam_event_id', 'bank_soal_id', 'end_at', 'duration', 'enforce_focus')
                ->with([
                'bankSoal:id,name,subject_id',
                'bankSoal.subject:id,name,religion_id',
                'examEvent:id,name'
            ])->findOrFail($scheduleId);
        });
        $now = Carbon::now();

        $subject = $schedule->bankSoal->subject;
        abort_if($subject && $subject->religion_id !== null && $subject->religion_id != $siswa->religion_id, 403, 'Ujian ini tidak tersedia (Beda Agama).');

        abort_if($now->gt($schedule->end_at), 403, 'Waktu ujian telah berakhir.');

        // Validasi Sesi Siswa
        $siswaSession = \App\Models\ExamSession::where('name', $siswa->sesi)->first();
        if ($siswaSession) {
            $curTime = $now->format('H:i:s');
            abort_if($curTime < $siswaSession->start_time, 403, 'Sesi Anda belum dimulai.');
            abort_if($curTime > $siswaSession->end_time, 403, 'Waktu sesi Anda telah berakhir.');
        }

        // Cek apakah sudah selesai atau diblokir
        $result = ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswa->id)
            ->first();

        if ($result) {
            if ($result->status === 'blocked') {
                if ($request->ajax()) return response()->json(['error' => 'Ujian diblokir.'], 403);
                return redirect()->route('siswa.dashboard')->with('error', 'Akses ditolak. Ujian Anda telah diblokir.');
            }
            if ($result->finished_at) {
                if ($request->ajax()) return response()->json(['error' => 'Sudah selesai.'], 403);
                return redirect()->route('siswa.dashboard')->with('error', 'Anda sudah menyelesaikan ujian ini.');
            }
        }

        // Ambil semua soal dari cache bank soal (0 query database jika sudah tercache di RAM/Cache)
        $bankQuestions = Cache::remember("bank_soal_questions_{$schedule->bank_soal_id}", 3600, function () use ($schedule) {
            return Question::where('bank_soal_id', $schedule->bank_soal_id)->get()->keyBy('id');
        });

        // Ambil urutan soal per siswa (urutan tetap tersimpan di session)
        $sessionQKey = "exam_questions_{$scheduleId}_{$siswa->id}";
        if (!session()->has($sessionQKey)) {
            if ($schedule->randomize_questions ?? true) {
                $ids = $bankQuestions->keys()->shuffle()->values()->toArray();
            } else {
                $ids = $bankQuestions->keys()->values()->toArray();
            }
            session([$sessionQKey => $ids]);
        }
        $questionIds = session($sessionQKey);
        $total = count($questionIds);

        $no = max(1, min((int)$no, $total));
        $questionId = $questionIds[$no - 1] ?? null;
        $rawQuestion = $questionId ? ($bankQuestions->get($questionId) ?? Question::findOrFail($questionId)) : Question::findOrFail($questionIds[0]);
        $question = clone $rawQuestion;

        // Optimasi: Tambahkan loading="lazy" ke semua img tag di question_text
        $question->question_text = preg_replace('/<img/i', '<img loading="lazy"', $question->question_text);
        if ($question->option_a)
            $question->option_a = preg_replace('/<img/i', '<img loading="lazy"', $question->option_a);
        if ($question->option_b)
            $question->option_b = preg_replace('/<img/i', '<img loading="lazy"', $question->option_b);
        if ($question->option_c)
            $question->option_c = preg_replace('/<img/i', '<img loading="lazy"', $question->option_c);
        if ($question->option_d)
            $question->option_d = preg_replace('/<img/i', '<img loading="lazy"', $question->option_d);
        if ($question->option_e)
            $question->option_e = preg_replace('/<img/i', '<img loading="lazy"', $question->option_e);

        // Jawaban yang sudah diisi siswa (pilih kolom yang diperlukan saja)
        $answers = ExamAnswer::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswa->id)
            ->select('id', 'question_id', 'answer', 'is_flagged')
            ->get()
            ->keyBy('question_id');

        // Hitung sisa waktu (detik)
        $sessionKey = "exam_start_{$scheduleId}_{$siswa->id}";
        $startedAt = Carbon::parse(session($sessionKey, $now));
        $durationSec = $schedule->duration * 60;
        $elapsedSec = $now->diffInSeconds($startedAt);
        
        $examEnd = $schedule->end_at;
        if ($siswaSession) {
            $sessionEnd = Carbon::createFromFormat('H:i:s', $siswaSession->end_time);
            if ($sessionEnd->lt($examEnd)) {
                $examEnd = $sessionEnd;
            }
        }
        $remainingSec = max(0, min($durationSec - $elapsedSec, $examEnd->diffInSeconds($now)));

        $statusMap = [];
        foreach ($questionIds as $idx => $qid) {
            $ans = $answers->get($qid);
            $statusMap[$idx + 1] = [
                'answered' => $ans ? !is_null($ans->answer) : false,
                'flagged' => $ans ? (bool)$ans->is_flagged : false
            ];
        }

                // Hide answer and format complex options
        unset($question->answer); // Never send the answer to the student!
        if (in_array($question->type, ['MJ', 'MT']) && $question->complex_options) {
            $opts = json_decode($question->complex_options, true);
            if ($question->type === 'MJ') {
                $right = $opts['right'] ?? [];
                // Use a seeded shuffle based on question id so it doesn't change on reload
                srand($question->id);
                shuffle($right);
                srand(); // reset
                $question->mj_left = $opts['left'] ?? [];
                $question->mj_right = $right;
            } elseif ($question->type === 'MT') {
                $items = $opts['items'] ?? [];
                srand($question->id);
                shuffle($items);
                srand();
                $question->mt_items = $items;
            }
            unset($question->complex_options);
        }
        
        // Ensure answer string is parsed properly for frontend
        $currAns = $answers->get($questionId)?->answer;
        if (in_array($question->type, ['PGK', 'MT', 'MJ'])) {
            $currAns = json_decode($currAns, true) ?: $currAns;
        }

        if ($request->ajax()) {
            return response()->json([
                'question' => $question,
                'current_no' => $no,
                'total' => $total,
                'current_answer' => $currAns,
                'current_flagged' => (bool)$answers->get($questionId)?->is_flagged,
                'remaining_sec' => $remainingSec,
                'status_map' => $statusMap
            ]);
        }

        return view('siswa.exam', compact(
            'siswa', 'schedule', 'question',
            'no', 'total', 'questionIds', 'answers', 'remainingSec'
        ));
    }

    /** Simpan jawaban (AJAX) */
        public function answer(Request $request, $scheduleId)
    {
        $siswa = Auth::guard('siswa')->user();
        $ans = $request->answer;
        if (is_array($ans)) {
            $ans = json_encode($ans);
        }
        
        \App\Models\ExamAnswer::updateOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswa->id, 'question_id' => $request->question_id],
            ['answer' => $ans, 'is_flagged' => $request->boolean('is_flagged')]
        );

        if ($request->ajax()) {
            return response()->json(['success' => true, 'answered' => !empty($ans), 'flagged' => $request->boolean('is_flagged')]);
        }
        return redirect()->back();
    }

    /** Selesai ujian */
    public function finish(Request $request, $scheduleId)
    {
        $siswa = Auth::guard('siswa')->user();
        $schedule = ExamSchedule::with('bankSoal')->findOrFail($scheduleId);

        $result = ExamResult::where('exam_schedule_id', $scheduleId)->where('siswa_id', $siswa->id)->first();
        if ($result && $result->finished_at) {
            session()->forget(["exam_start_{$scheduleId}_{$siswa->id}", "exam_questions_{$scheduleId}_{$siswa->id}"]);
            return view('siswa.exam-result', ['schedule' => $schedule, 'siswa' => $siswa, 'total' => $result->total_questions, 'correct' => $result->correct_answers, 'score' => $result->score]);
        }

        // KUNCI LANGSUNG STATUS SELESAI (< 5 ms) untuk mencegah multi-submit saat lonjakan traffic
        ExamResult::updateOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswa->id],
            ['finished_at' => now(), 'status' => 'finished']
        );
        session()->forget(["exam_start_{$scheduleId}_{$siswa->id}", "exam_questions_{$scheduleId}_{$siswa->id}"]);

        // Jika jadwal ujian tidak menampilkan nilai & persentase ke siswa, oper ke background queue
        if (!$schedule->show_score && !$schedule->show_percentage) {
            \App\Jobs\CalculateExamScoreJob::dispatch($scheduleId, $siswa->id);

            $totalQ = Question::where('bank_soal_id', $schedule->bank_soal_id)->count();
            return view('siswa.exam-result', [
                'schedule' => $schedule,
                'siswa' => $siswa,
                'total' => $totalQ,
                'correct' => 0,
                'score' => 0
            ]);
        }

        $questions = Question::where('bank_soal_id', $schedule->bank_soal_id)
            ->leftJoin('exam_answers', function ($j) use ($scheduleId, $siswa) {
            $j->on('questions.id', '=', 'exam_answers.question_id')->where('exam_answers.exam_schedule_id', $scheduleId)->where('exam_answers.siswa_id', $siswa->id);
        })->select('questions.id', 'questions.type', 'questions.answer as cor_ans', 'questions.complex_options', 'exam_answers.answer as stu_ans', 'exam_answers.score as es_score')->get();

                $totalScore = 0;
        $correctPG = 0;
        $totalQ = $questions->count();
        $grouped = $questions->groupBy('type');
        
        // Helper map from DB types to setting percent column
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
        ExamResult::updateOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswa->id], 
            ['finished_at' => now(), 'status' => 'finished', 'total_questions' => $totalQ, 'correct_answers' => $correctPG, 'score' => $score]
        );
        session()->forget(["exam_start_{$scheduleId}_{$siswa->id}", "exam_questions_{$scheduleId}_{$siswa->id}"]);

        return view('siswa.exam-result', ['schedule' => $schedule, 'siswa' => $siswa, 'total' => $totalQ, 'correct' => $correctPG, 'score' => $score]);
    }

    public function block(Request $request, $scheduleId)
    {
        $siswa = Auth::guard('siswa')->user();
        ExamResult::updateOrCreate(
            ['exam_schedule_id' => $scheduleId, 'siswa_id' => $siswa->id],
            ['finished_at' => now(), 'status' => 'blocked']
        );
        session()->forget(["exam_start_{$scheduleId}_{$siswa->id}", "exam_questions_{$scheduleId}_{$siswa->id}"]);
        
        Auth::guard('siswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login')->with('error', 'Anda dikeluarkan karena terindikasi melakukan kecurangan (Pindah Tab/Membuka Aplikasi Lain). Silakan hubungi pengawas untuk mereset ujian Anda.');
    }
}