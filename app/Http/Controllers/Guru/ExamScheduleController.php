<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExamSchedule;
use App\Models\ExamEvent;
use App\Models\BankSoal;

class ExamScheduleController extends Controller
{
    private function authorizedSchedule(ExamSchedule $examSchedule)
    {
        $guruSubjectIds = auth('guru')->user()->subjects->pluck('id')->toArray();
        if (!in_array($examSchedule->bankSoal->subject_id, $guruSubjectIds)) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function index(Request $request)
    {
        $guruSubjectIds = auth('guru')->user()->subjects->pluck('id')->toArray();
        
        if ($request->ajax()) {
            $data = ExamSchedule::with(['examEvent', 'bankSoal'])
                ->whereHas('bankSoal', function ($q) use ($guruSubjectIds) {
                    $q->whereIn('subject_id', $guruSubjectIds);
                })
                ->select('exam_schedules.*')->get();
                
            // Refresh tokens if needed before showing to admin
            foreach ($data as $item) {
                $item->maybeRefreshToken();
            }
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('event_name', function ($row) {
                return $row->examEvent->name ?? '-';
            })
                ->addColumn('bank_soal_name', function ($row) {
                return $row->bankSoal->name ?? '-';
            })
                ->addColumn('status_label', function ($row) {
                return $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Non-Aktif</span>';
            })
                ->addColumn('timing', function ($row) {
                return '<small>' . $row->start_at->format('d/m/Y H:i') . '</small><br><small>' . $row->end_at->format('d/m/Y H:i') . '</small>';
            })
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" title="Edit" data-bs-toggle="tooltip" 
                    data-id="' . $row->id . '"
                    data-exam_event_id="' . $row->exam_event_id . '"
                    data-bank_soal_id="' . $row->bank_soal_id . '"
                    data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '"
                    data-start_at="' . $row->start_at->format('Y-m-d\TH:i') . '"
                    data-end_at="' . $row->end_at->format('Y-m-d\TH:i') . '"
                    data-duration="' . $row->duration . '"
                    data-is_active="' . $row->is_active . '"
                ><i class="align-middle" data-feather="edit-2"></i></button>';
                $btn .= '<form action="' . route('guru.exam-schedule.destroy', $row->id) . '" method="POST" class="d-inline">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger btn-delete"  title="Hapus"><i class="align-middle" data-feather="trash-2"></i></button>
                                </form>';
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['status_label', 'timing', 'action'])
                ->make(true);
        }
        $examEvents = ExamEvent::where('is_active', true)->get();
        $bankSoals = BankSoal::where('status', true)->whereIn('subject_id', $guruSubjectIds)->get();
        return view('guru.exam-schedule.index', compact('examEvents', 'bankSoals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'exam_event_id' => 'required|exists:exam_events,id',
            'bank_soal_id' => 'required|exists:bank_soals,id',
            'name' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'duration' => 'required|numeric|min:1',
        ]);
        
        $bankSoal = BankSoal::findOrFail($request->bank_soal_id);
        $guruSubjectIds = auth('guru')->user()->subjects->pluck('id')->toArray();
        if (!in_array($bankSoal->subject_id, $guruSubjectIds)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        ExamSchedule::create($data);

        return redirect()->route('guru.exam-schedule.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $examSchedule = ExamSchedule::findOrFail($id);
        $this->authorizedSchedule($examSchedule);

        $request->validate([
            'exam_event_id' => 'required|exists:exam_events,id',
            'bank_soal_id' => 'required|exists:bank_soals,id',
            'name' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'duration' => 'required|numeric|min:1',
        ]);
        
        $bankSoal = BankSoal::findOrFail($request->bank_soal_id);
        $guruSubjectIds = auth('guru')->user()->subjects->pluck('id')->toArray();
        if (!in_array($bankSoal->subject_id, $guruSubjectIds)) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $examSchedule->update($data);

        return redirect()->route('guru.exam-schedule.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $examSchedule = ExamSchedule::findOrFail($id);
        $this->authorizedSchedule($examSchedule);
        
        $examSchedule->delete();
        return redirect()->route('guru.exam-schedule.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }
}

