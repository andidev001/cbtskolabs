<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use App\Models\BankSoal;
use App\Models\ExamResult;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $guru = Auth::guard('guru')->user();
        $mapels = $guru->subjects()->pluck('subjects.id');

        // Bank soal milik mapel guru
        $totalBankSoal = BankSoal::whereIn('subject_id', $mapels)->count();

        // Jadwal ujian yang menggunakan bank soal milik guru
        $totalJadwal = ExamSchedule::whereHas('bankSoal', function ($q) use ($mapels) {
            $q->whereIn('subject_id', $mapels);
        })->count();

        // Jumlah hasil ujian
        $totalNilai = ExamResult::whereHas('examSchedule.bankSoal', function ($q) use ($mapels) {
            $q->whereIn('subject_id', $mapels);
        })->count();

        return view('guru.dashboard', compact('guru', 'totalBankSoal', 'totalJadwal', 'totalNilai'));
    }
}
