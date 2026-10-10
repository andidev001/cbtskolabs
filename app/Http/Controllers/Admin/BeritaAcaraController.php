<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;
use App\Models\Siswa;
use App\Models\ExamResult;
use App\Models\BeritaAcara;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class BeritaAcaraController extends Controller
{
        public function index(Request $request)
    {
        if ($request->ajax()) {
            $schedules = ExamSchedule::with(['examEvent', 'bankSoal', 'groups'])
                ->where('is_active', true)
                ->whereDate('start_at', '<=', now()->toDateString())
                ->whereDate('end_at', '>=', now()->toDateString())
                ->get();
                
            $data = [];
            foreach ($schedules as $schedule) {
                foreach ($schedule->groups as $group) {
                    $ba = BeritaAcara::where('exam_schedule_id', $schedule->id)->where('group_id', $group->id)->first();
                    $data[] = [
                        'id' => $schedule->id,
                        'group_id' => $group->id,
                        'name' => $schedule->name,
                        'event_name' => $schedule->examEvent->name ?? '-',
                        'bank_soal_name' => $schedule->bankSoal->name ?? '-',
                        'group_name' => $group->name,
                        'ba' => $ba
                    ];
                }
            }

            return DataTables::of(collect($data))
                ->addIndexColumn()
                ->addColumn('action', function ($row) use ($request) {
                    $storeUrl = url($request->segment(1) . '/berita-acara/' . $row['id'] . '/store/' . $row['group_id']);
                    $pdfUrl = url($request->segment(1) . '/berita-acara/' . $row['id'] . '/cetak-pdf/' . $row['group_id']);
                    
                    $ba = $row['ba'];
                    $btnPdf = '<button class="btn btn-sm btn-danger text-white btn-view-pdf" data-url="'.$pdfUrl.'" title="Cetak PDF" data-bs-toggle="modal" data-bs-target="#modalPdfViewer"><i class="align-middle" data-feather="printer" style="width: 14px; height: 14px;"></i></button>';
                    
                    $scheduleName = $row['name'] . ' - ' . $row['group_name'];
                    if ($ba) {
                        $btnEdit = '<button class="btn btn-sm btn-success text-white btn-edit-ba" 
                            data-url="' . $storeUrl . '" 
                            data-schedule="' . $scheduleName . '" 
                            data-ruang="' . htmlspecialchars($ba->ruang, ENT_QUOTES) . '" 
                            data-sesi="' . htmlspecialchars($ba->sesi, ENT_QUOTES) . '" 
                            data-proktor="' . htmlspecialchars($ba->proktor, ENT_QUOTES) . '" 
                            data-pengawas="' . htmlspecialchars($ba->pengawas, ENT_QUOTES) . '" 
                            data-catatan="' . htmlspecialchars($ba->catatan, ENT_QUOTES) . '" 
                            title="Edit Berita Acara" data-bs-toggle="modal" data-bs-target="#modalBeritaAcara">
                            <i class="align-middle" data-feather="edit" style="width: 14px; height: 14px;"></i></button>';
                            
                        return '<div class="d-flex gap-1 justify-content-center">' . $btnEdit . $btnPdf . '</div>';
                    } else {
                        $btnIsi = '<button class="btn btn-sm btn-info text-white btn-edit-ba" data-url="' . $storeUrl . '" data-schedule="' . $scheduleName . '" title="Isi Berita Acara" data-bs-toggle="modal" data-bs-target="#modalBeritaAcara"><i class="align-middle" data-feather="file-plus" style="width: 14px; height: 14px;"></i></button>';
                        return '<div class="d-flex gap-1 justify-content-center">' . $btnIsi . $btnPdf . '</div>';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $prefix = strtolower(explode('\\', __NAMESPACE__)[3]); // App\Http\Controllers\Admin -> admin
        return view($prefix . '.berita-acara.index');
    }

        public function store(Request $request, $id, $groupId = null)
    {
        $request->validate([
            'ruang' => 'nullable|string|max:255',
            'sesi' => 'nullable|string|max:255',
            'proktor' => 'nullable|string|max:255',
            'pengawas' => 'nullable|string|max:255',
            'catatan' => 'nullable|string',
        ]);

        BeritaAcara::updateOrCreate(
            ['exam_schedule_id' => $id, 'group_id' => $groupId],
            [
                'ruang' => $request->ruang,
                'sesi' => $request->sesi,
                'proktor' => $request->proktor,
                'pengawas' => $request->pengawas,
                'catatan' => $request->catatan,
            ]
        );

        return redirect()->back()->with('success', 'Berita Acara berhasil disimpan.');
    }

        public function cetakPdf(Request $request, $id, $groupId = null)
    {
        $schedule = ExamSchedule::with(['bankSoal.subject', 'examEvent'])->findOrFail($id);
        
        $groups = \App\Models\Group::where('id', $groupId)->get();
        if ($groups->isEmpty()) abort(404, 'Grup tidak ditemukan.');
        
        $ba = BeritaAcara::where('exam_schedule_id', $id)->where('group_id', $groupId)->first();
        $students = Siswa::where('group_id', $groupId)->orderBy('name')->get();
        
        $results = ExamResult::where('exam_schedule_id', $id)
            ->whereIn('siswa_id', $students->pluck('id'))
            ->get()->keyBy('siswa_id');
            
        $pdf = Pdf::loadView('shared.berita-acara-pdf', compact(
            'schedule', 'ba', 'groups', 'students', 'results'
        ));

        return $pdf->stream('Berita_Acara_'.$schedule->id.'_Kelas_'.$groupId.'.pdf');
    }
}