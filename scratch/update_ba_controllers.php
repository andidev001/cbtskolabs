<?php

$stub = <<<CODE
<?php

namespace App\Http\Controllers\__NAMESPACE__;

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
    public function index(Request \$request)
    {
        if (\$request->ajax()) {
            \$data = ExamSchedule::with(['examEvent', 'bankSoal', 'beritaAcara'])
                ->where('is_active', true)
                ->whereDate('start_at', '<=', now()->toDateString())
                ->whereDate('end_at', '>=', now()->toDateString())
                ->select('exam_schedules.*');

            return DataTables::of(\$data)
                ->addIndexColumn()
                ->addColumn('event_name', function (\$row) {
                    return \$row->examEvent->name ?? '-';
                })
                ->addColumn('bank_soal_name', function (\$row) {
                    return \$row->bankSoal->name ?? '-';
                })
                ->addColumn('action', function (\$row) use (\$request) {
                    \$storeUrl = url(\$request->segment(1) . '/berita-acara/' . \$row->id . '/store');
                    \$pdfUrl = url(\$request->segment(1) . '/berita-acara/' . \$row->id . '/cetak-pdf');
                    
                    \$ba = \$row->beritaAcara;
                    
                    if (\$ba) {
                        \$btnEdit = '<button class="btn btn-sm btn-success text-white btn-edit-ba" 
                            data-url="' . \$storeUrl . '" 
                            data-schedule="' . \$row->name . '" 
                            data-ruang="' . htmlspecialchars(\$ba->ruang, ENT_QUOTES) . '" 
                            data-sesi="' . htmlspecialchars(\$ba->sesi, ENT_QUOTES) . '" 
                            data-proktor="' . htmlspecialchars(\$ba->proktor, ENT_QUOTES) . '" 
                            data-pengawas="' . htmlspecialchars(\$ba->pengawas, ENT_QUOTES) . '" 
                            data-catatan="' . htmlspecialchars(\$ba->catatan, ENT_QUOTES) . '" 
                            title="Edit Berita Acara" data-bs-toggle="modal" data-bs-target="#modalBeritaAcara">
                            <i class="align-middle" data-feather="edit" style="width: 14px; height: 14px;"></i> Edit</button>';
                            
                        \$btnPdf = '<button class="btn btn-sm btn-danger text-white btn-view-pdf" data-url="'.\$pdfUrl.'" title="Lihat PDF" data-bs-toggle="modal" data-bs-target="#modalPdfViewer"><i class="align-middle" data-feather="file-text" style="width: 14px; height: 14px;"></i> Cetak PDF</button>';
                        
                        return '<div class="d-flex gap-1 justify-content-center">' . \$btnEdit . \$btnPdf . '</div>';
                    } else {
                        return '<div class="text-center">
                                    <button class="btn btn-sm btn-info text-white btn-edit-ba" data-url="' . \$storeUrl . '" data-schedule="' . \$row->name . '" title="Isi Berita Acara" data-bs-toggle="modal" data-bs-target="#modalBeritaAcara"><i class="align-middle" data-feather="file-plus" style="width: 14px; height: 14px;"></i> Isi Berita Acara</button>
                                </div>';
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('__PREFIX__.berita-acara.index');
    }

    public function store(Request \$request, \$id)
    {
        \$request->validate([
            'ruang' => 'required|string|max:255',
            'sesi' => 'required|string|max:255',
            'proktor' => 'required|string|max:255',
            'pengawas' => 'required|string|max:255',
            'catatan' => 'required|string',
        ]);

        BeritaAcara::updateOrCreate(
            ['exam_schedule_id' => \$id],
            [
                'ruang' => \$request->ruang,
                'sesi' => \$request->sesi,
                'proktor' => \$request->proktor,
                'pengawas' => \$request->pengawas,
                'catatan' => \$request->catatan,
            ]
        );

        return redirect()->back()->with('success', 'Berita Acara berhasil disimpan.');
    }

    public function cetakPdf(Request \$request, \$id)
    {
        \$schedule = ExamSchedule::with(['bankSoal', 'examEvent', 'groups', 'beritaAcara'])->findOrFail(\$id);
        \$ba = \$schedule->beritaAcara;

        if (!\$ba) {
            return abort(404, 'Berita Acara belum diisi.');
        }

        \$assignedGroupIds = \$schedule->groups->pluck('id')->toArray();
        \$query = Siswa::whereIn('group_id', \$assignedGroupIds);
        \$students = \$query->orderBy('name')->get();
        \$total_students = \$students->count();
        
        \$results = ExamResult::where('exam_schedule_id', \$id)
            ->whereIn('siswa_id', \$students->pluck('id'))
            ->get()->keyBy('siswa_id');
            
        \$total_present = \$results->count();
        \$total_absent = \$total_students - \$total_present;
        
        \$absent_students = [];
        foreach (\$students as \$student) {
            if (!\$results->has(\$student->id)) {
                \$absent_students[] = \$student->name;
            }
        }

        \$pdf = Pdf::loadView('shared.berita-acara-pdf', compact(
            'schedule', 'ba', 'total_students', 'total_present', 'total_absent', 'absent_students'
        ));

        // Stream PDF inline
        return \$pdf->stream('Berita_Acara_'.\$schedule->id.'.pdf');
    }
}
CODE;

file_put_contents('app/Http/Controllers/Admin/BeritaAcaraController.php', str_replace(['__NAMESPACE__', '__PREFIX__'], ['Admin', 'admin'], $stub));
file_put_contents('app/Http/Controllers/Guru/BeritaAcaraController.php', str_replace(['__NAMESPACE__', '__PREFIX__'], ['Guru', 'guru'], $stub));

echo "Controllers updated.\n";
