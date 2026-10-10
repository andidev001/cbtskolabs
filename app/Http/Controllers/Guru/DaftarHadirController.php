<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;
use App\Models\Siswa;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class DaftarHadirController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExamSchedule::with(['examEvent', 'bankSoal', 'groups'])
                ->where('is_active', true)
                ->whereDate('start_at', '<=', now()->toDateString())
                ->whereDate('end_at', '>=', now()->toDateString())
                ->select('exam_schedules.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('event_name', function ($row) {
                    return $row->examEvent->name ?? '-';
                })
                ->addColumn('bank_soal_name', function ($row) {
                    return $row->bankSoal->name ?? '-';
                })
                ->addColumn('action', function ($row) use ($request) {
                    $pdfUrl = url($request->segment(1) . '/daftar-hadir/' . $row->id . '/cetak-pdf');
                    return '<div class="text-center">
                                <button class="btn btn-sm btn-danger text-white btn-view-pdf" data-url="' . $pdfUrl . '" title="Cetak Daftar Hadir" data-bs-toggle="modal" data-bs-target="#modalPdfViewer"><i class="align-middle" data-feather="printer" style="width: 14px; height: 14px;"></i> Cetak</button>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('guru.daftar-hadir.index');
    }

    public function cetakPdf(Request $request, $id)
    {
        $schedule = ExamSchedule::with(['bankSoal.subject', 'examEvent', 'groups', 'beritaAcara'])->findOrFail($id);
        $ba = $schedule->beritaAcara;

        $groups = $schedule->groups;
        
        $assignedGroupIds = $groups->pluck('id')->toArray();
        $allStudents = Siswa::with('group')->whereIn('group_id', $assignedGroupIds)->orderBy('name')->get();
        
        // Group students by group_id for the view
        $studentsByGroup = $allStudents->groupBy('group_id');

        $pdf = Pdf::loadView('shared.daftar-hadir-pdf', compact('schedule', 'ba', 'groups', 'studentsByGroup'));

        return $pdf->stream('Daftar_Hadir_'.$schedule->id.'.pdf');
    }
}