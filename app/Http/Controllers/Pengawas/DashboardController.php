<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExamSchedule;

class DashboardController extends Controller
{
    public function index()
    {
        $active_exams_count = ExamSchedule::where('is_active', true)
            ->whereDate('start_at', '<=', now()->toDateString())
            ->whereDate('end_at', '>=', now()->toDateString())
            ->count();

        return view('pengawas.dashboard', compact('active_exams_count'));
    }
}