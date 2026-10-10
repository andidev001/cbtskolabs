<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $siswa = Auth::guard('siswa')->user();
        $now = Carbon::now();

        // Ambil hasil ujian siswa untuk cek status penyelesaian
        $results = \App\Models\ExamResult::where('siswa_id', $siswa->id)
            ->get()->keyBy('exam_schedule_id');

        $siswaSession = \App\Models\ExamSession::where('name', $siswa->sesi)->first();

        // Ambil jadwal ujian dengan kolom terpilih saja
        $schedules = ExamSchedule::select('id', 'name', 'exam_event_id', 'bank_soal_id', 'start_at', 'end_at', 'duration', 'token', 'show_score', 'show_percentage', 'show_answer', 'enforce_focus')
            ->with([
            'examEvent:id,name',
            'bankSoal:id,name,subject_id',
            'bankSoal.subject:id,name,religion_id'
        ])
            ->whereHas('groups', function ($q) use ($siswa) {
            $q->where('groups.id', $siswa->group_id);
        })
            ->whereHas('bankSoal.subject', function($q) use ($siswa) {
                $q->whereNull('religion_id')
                  ->orWhere('religion_id', $siswa->religion_id);
            })
            ->where('is_active', true)
            ->orderBy('start_at')
            ->get()
            ->map(function ($s) use ($now, $results, $siswaSession) {
            $res = $results->get($s->id);
            $curTime = $now->format('H:i:s');
            
            $s->status_ujian = match (true) {
                    $res && $res->status === 'blocked' => 'blocked',
                    $res && $res->finished_at !== null => 'finished',
                    $now->lt($s->start_at) => 'upcoming', // belum mulai
                    $now->gt($s->end_at) => 'finished', // waktu habis
                    $siswaSession && $curTime < $siswaSession->start_time => 'upcoming', // Sesi belum mulai
                    $siswaSession && $curTime > $siswaSession->end_time => 'finished', // Sesi sudah lewat
                    default => 'ongoing',
                };
            return $s;
        });

        $upcoming = $schedules->where('status_ujian', 'upcoming');
        $ongoing = $schedules->where('status_ujian', 'ongoing');
        $finished = $schedules->whereIn('status_ujian', ['finished', 'blocked']);

        return view('siswa.dashboard', compact('siswa', 'schedules', 'upcoming', 'ongoing', 'finished', 'now', 'results', 'siswaSession'));
    }
}
