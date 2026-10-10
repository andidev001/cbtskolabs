<?php

$stub = <<<CODE
<?php

namespace App\Http\Controllers\__NAMESPACE__;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExamSchedule;
use App\Models\Siswa;
use App\Models\ExamResult;
use Yajra\DataTables\Facades\DataTables;

class BeritaAcaraController extends Controller
{
    public function index(Request \$request)
    {
        if (\$request->ajax()) {
            \$data = ExamSchedule::with(['examEvent', 'bankSoal'])
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
                    \$url = url(\$request->segment(1) . '/berita-acara/' . \$row->id . '/cetak');
                    return '<div class="text-center">
                                <button class="btn btn-sm btn-info text-white btn-print-ba" data-url="' . \$url . '" data-schedule="' . \$row->name . '" title="Cetak Berita Acara" data-bs-toggle="modal" data-bs-target="#modalBeritaAcara"><i class="align-middle" data-feather="printer" style="width: 14px; height: 14px;"></i> Cetak</button>
                            </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('__PREFIX__.berita-acara.index');
    }

    public function cetak(Request \$request, \$id)
    {
        \$schedule = ExamSchedule::with(['bankSoal', 'examEvent', 'groups'])->findOrFail(\$id);
        
        \$assignedGroupIds = \$schedule->groups->pluck('id')->toArray();
        \$query = Siswa::whereIn('group_id', \$assignedGroupIds);
        
        if (\$request->has('ruang') && \$request->ruang != '') {
            \$query->where('ruang', \$request->ruang);
        }
        if (\$request->has('sesi') && \$request->sesi != '') {
            \$query->where('sesi', \$request->sesi);
        }
        
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

        return view('shared.berita-acara', compact(
            'schedule', 'total_students', 'total_present', 'total_absent', 'absent_students'
        ));
    }
}
CODE;

file_put_contents('app/Http/Controllers/Admin/BeritaAcaraController.php', str_replace(['__NAMESPACE__', '__PREFIX__'], ['Admin', 'admin'], $stub));
file_put_contents('app/Http/Controllers/Guru/BeritaAcaraController.php', str_replace(['__NAMESPACE__', '__PREFIX__'], ['Guru', 'guru'], $stub));

echo "Controllers created.\n";

