<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\ExamEvent;
use App\Models\ExamSchedule;
use App\Models\BankSoal;

class ExamEventController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExamEvent::select('exam_events.*');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status_label', function ($row) {
                return $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Non-Aktif</span>';
            })
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<a href="' . route('admin.exam-event.show', $row->id) . '" class="btn btn-sm btn-info text-white me-1" title="Detail" data-bs-toggle="tooltip"><i class="align-middle" data-feather="eye"></i></a>';
                $btn .= '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" title="Edit" data-bs-toggle="tooltip" 
                    data-id="' . $row->id . '"
                    data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '"
                    data-description="' . htmlspecialchars($row->description, ENT_QUOTES) . '"
                    data-is_active="' . $row->is_active . '"
                ><i class="align-middle" data-feather="edit-2"></i></button>';
                $btn .= '<form action="' . route('admin.exam-event.destroy', $row->id) . '" method="POST" class="d-inline">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger btn-delete"  title="Hapus"><i class="align-middle" data-feather="trash-2"></i></button>
                                </form>';
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['status_label', 'action'])
                ->make(true);
        }
        return view('admin.exam-event.index');
    }

    public function show(string $id)
    {
        $examEvent = ExamEvent::findOrFail($id);
        $schedules = ExamSchedule::with('bankSoal')
            ->where('exam_event_id', $id)
            ->latest()
            ->get();
        $bankSoals = BankSoal::where('status', true)->get();
        return view('admin.exam-event.show', compact('examEvent', 'schedules', 'bankSoals'));
    }

    public function create()
    {
        return view('admin.exam-event.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ExamEvent::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('admin.exam-event.index')->with('success', 'Event ujian berhasil dibuat.');
    }

    public function edit(string $id)
    {
        $examEvent = ExamEvent::findOrFail($id);
        return view('admin.exam-event.edit', compact('examEvent'));
    }

    public function update(Request $request, string $id)
    {
        $examEvent = ExamEvent::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $examEvent->update($data);

        return redirect()->route('admin.exam-event.index')->with('success', 'Event ujian berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $examEvent = ExamEvent::findOrFail($id);
        $examEvent->delete();
        return redirect()->route('admin.exam-event.index')->with('success', 'Event ujian berhasil dihapus.');
    }
}
