<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Religion;
use Illuminate\Http\Request;

class ReligionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Religion::select('religions.*');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->name) . '" title="Edit" data-bs-toggle="tooltip"><i class="align-middle" data-feather="edit-2"></i></button>';
                $btn .= '<form action="' . route('admin.religion.destroy', $row->id) . '" method="POST" class="d-inline">
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
        return view('admin.religion.index');
    }

    public function create()
    {
        return view('admin.religion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:religions',
        ]);

        Religion::create($request->all());

        return redirect()->route('admin.religion.index')->with('success', 'Agama berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $religion = Religion::findOrFail($id);
        return view('admin.religion.edit', compact('religion'));
    }

    public function update(Request $request, $id)
    {
        $religion = Religion::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:religions,name,' . $religion->id,
        ]);

        $religion->update($request->all());

        return redirect()->route('admin.religion.index')->with('success', 'Agama berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $religion = Religion::findOrFail($id);
        if ($religion->siswas()->count() > 0) {
            return back()->with('error', 'Agama tidak bisa dihapus karena masih digunakan oleh peserta.');
        }
        $religion->delete();
        return redirect()->route('admin.religion.index')->with('success', 'Agama berhasil dihapus.');
    }
}
