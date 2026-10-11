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
                $btn .= '<form action="' . route('admin.exam-schedule.destroy', $row->id) . '" method="POST" class="d-inline">
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

        // Generate a random token if not provided or just keep it null?
        // Usually, CBT tokens are manually generated or assigned.

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

        $examSchedule->update($data);

        return redirect()->route('admin.exam-schedule.index')->with('success', 'Jadwal ujian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $examSchedule = ExamSchedule::findOrFail($id);
        $examSchedule->delete();
        return redirect()->route('admin.exam-schedule.index')->with('success', 'Jadwal ujian berhasil dihapus.');
    }
}
