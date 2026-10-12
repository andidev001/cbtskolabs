<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\BankSoal;
use App\Models\ExamEvent;
use App\Models\ExamSchedule;
use App\Models\ExamResult;
use App\Models\Group;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // Stats
        $countSiswa = Siswa::count();
        $countBankSoal = BankSoal::count();
        $countEvent = ExamEvent::count();
        $countOngoing = ExamSchedule::where('is_active', true)->count();

        // Data for Chart: Siswa per Group
        $groups = Group::withCount('siswas')->get();
        $chartData = [
            'labels' => $groups->pluck('name'),
            'data' => $groups->pluck('siswas_count'),
        ];

        // Data for Chart: Siswa per Jurusan
        $jurusans = \App\Models\Jurusan::withCount('siswas')->get();
        $jurusanChartData = [
            'labels' => $jurusans->pluck('name'),
            'data' => $jurusans->pluck('siswas_count'),
        ];

        // Active & Upcoming Exams (ujian aktif yang sedang berlangsung atau akan datang)
        $upcomingExams = ExamSchedule::where('end_at', '>=', $now)
            ->where('is_active', true)
            ->orderBy('start_at', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'countSiswa',
            'countBankSoal',
            'countEvent',
            'countOngoing',
            'chartData',
            'jurusanChartData',
            'upcomingExams',
            'now'
        ));
    }
}
