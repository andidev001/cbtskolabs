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

        // Bank soal milik guru ini
        $totalBankSoal = BankSoal::forGuru($guru)->count();

        // Jadwal ujian yang menggunakan bank soal milik guru ini
        $totalJadwal = ExamSchedule::whereHas('bankSoal', function ($q) use ($guru) {
            $q->forGuru($guru);
        })->count();

        // Jumlah hasil ujian dari jadwal milik guru ini
        $totalNilai = ExamResult::whereHas('examSchedule.bankSoal', function ($q) use ($guru) {
            $q->forGuru($guru);
        })->count();

        return view('guru.dashboard', compact('guru', 'totalBankSoal', 'totalJadwal', 'totalNilai'));
    }
}
