<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subject::select('subjects.*');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" data-id="' . $row->id . '" data-code="' . htmlspecialchars($row->code) . '" data-name="' . htmlspecialchars($row->name) . '" title="Edit" data-bs-toggle="tooltip"><i class="align-middle" data-feather="edit-2"></i></button>';
                $btn .= '<form action="' . route('admin.subject.destroy', $row->id) . '" method="POST" class="d-inline">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button type="submit" class="btn btn-sm btn-danger btn-delete"  title="Hapus"><i class="align-middle" data-feather="trash-2"></i></button>
                                </form>';
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.subject.index');
    }

    public function create()
    {
        return view('admin.subject.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects',
        ]);

        Subject::create($request->all());

        return redirect()->route('admin.subject.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        return view('admin.subject.edit', compact('subject'));
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
        ]);

        $subject->update($request->all());

        return redirect()->route('admin.subject.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        return redirect()->route('admin.subject.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
