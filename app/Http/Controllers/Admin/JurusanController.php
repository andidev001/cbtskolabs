<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Jurusan::select('jurusans.*');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" data-id="' . $row->id . '" data-code="' . htmlspecialchars($row->code) . '" data-name="' . htmlspecialchars($row->name) . '" title="Edit" data-bs-toggle="tooltip"><i class="align-middle" data-feather="edit-2"></i></button>';
                $btn .= '<form action="' . route('admin.jurusan.destroy', $row->id) . '" method="POST" class="d-inline">
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
        return view('admin.jurusan.index');
    }

    public function create()
    {
        return view('admin.jurusan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:jurusans',
        ]);

        Jurusan::create($request->all());

        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:jurusans,code,' . $jurusan->id,
        ]);

        $jurusan->update($request->all());

        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        if ($jurusan->siswas()->count() > 0) {
            return back()->with('error', 'Jurusan tidak bisa dihapus karena masih memiliki peserta.');
        }
        $jurusan->delete();
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil dihapus.');
    }
}
