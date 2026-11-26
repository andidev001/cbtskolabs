<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExamSchedule;
use App\Models\ExamEvent;
use App\Models\BankSoal;

class ExamScheduleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExamSchedule::with(['examEvent', 'bankSoal'])->select('exam_schedules.*')->get();
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
                $btn = '<div class="d-flex gap-1 justify-content-center">';
                $btn .= '<a href="' . route('admin.exam-schedule.participants', $row->id) . '" class="btn btn-sm px-2 d-inline-flex align-items-center text-white" style="background-color: #3b31c1; border: none;" title="Mapping Peserta" data-bs-toggle="tooltip">
                    <i class="align-middle" data-feather="users" style="width: 13px; height: 13px;"></i>
                </a>';
                $btn .= '<button type="button" class="btn btn-sm btn-warning text-white px-2 btn-edit" style="background-color: #f3b123; border: none;" title="Edit" data-bs-toggle="tooltip" 
                    data-id="' . $row->id . '"
                    data-exam_event_id="' . $row->exam_event_id . '"
                    data-bank_soal_id="' . $row->bank_soal_id . '"
                    data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '"
                    data-start_at="' . $row->start_at->format('Y-m-d\TH:i') . '"
                    data-end_at="' . $row->end_at->format('Y-m-d\TH:i') . '"
                    data-duration="' . $row->duration . '"
                    data-is_active="' . $row->is_active . '"
                    data-randomize_questions="' . ($row->randomize_questions ? '1' : '0') . '"
                    data-randomize_options="' . ($row->randomize_options ? '1' : '0') . '"
                    data-enforce_focus="' . ($row->enforce_focus ? '1' : '0') . '"
                ><i class="align-middle" data-feather="edit-2" style="width: 13px; height: 13px;"></i></button>';
                $btn .= '<form action="' . route('admin.exam-schedule.destroy', $row->id) . '" method="POST" class="d-inline">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger px-2 btn-delete" style="background-color: #e35d5d; border: none;" title="Hapus"><i class="align-middle" data-feather="trash-2" style="width: 13px; height: 13px;"></i></button>
                                </form>';
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['status_label', 'timing', 'action'])
                ->make(true);
        }
        $examEvents = ExamEvent::where('is_active', true)->get();
        $bankSoals = BankSoal::where('status', true)->get();
        return view('admin.exam-schedule.index', compact('examEvents', 'bankSoals'));
    }

    public function create()
    {
        $examEvents = ExamEvent::where('is_active', true)->get();
        $bankSoals = BankSoal::where('status', true)->get();
        return view('admin.exam-schedule.create', compact('examEvents', 'bankSoals'));
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

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['randomize_questions'] = $request->has('randomize_questions') ? 1 : 0;
        $data['randomize_options'] = $request->has('randomize_options') ? 1 : 0;
        $data['enforce_focus'] = $request->has('enforce_focus') ? 1 : 0;

        ExamSchedule::create($data);

        return redirect()->route('admin.exam-schedule.index')->with('success', 'Jadwal ujian berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $examSchedule = ExamSchedule::findOrFail($id);
        $examEvents = ExamEvent::where('is_active', true)->get();
        $bankSoals = BankSoal::where('status', true)->get();
        return view('admin.exam-schedule.edit', compact('examSchedule', 'examEvents', 'bankSoals'));
    }

    public function update(Request $request, string $id)
    {
        $examSchedule = ExamSchedule::findOrFail($id);

        $request->validate([
            'exam_event_id' => 'required|exists:exam_events,id',
            'bank_soal_id' => 'required|exists:bank_soals,id',
            'name' => 'required|string|max:255',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'duration' => 'required|numeric|min:1',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['randomize_questions'] = $request->has('randomize_questions') ? 1 : 0;
        $data['randomize_options'] = $request->has('randomize_options') ? 1 : 0;
        $data['enforce_focus'] = $request->has('enforce_focus') ? 1 : 0;

        $examSchedule->update($data);
        \Illuminate\Support\Facades\Cache::forget("exam_sched_meta_{$examSchedule->id}");

        return redirect()->route('admin.exam-schedule.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $examSchedule = ExamSchedule::findOrFail($id);
        $examSchedule->delete();
        return redirect()->route('admin.exam-schedule.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }
}
