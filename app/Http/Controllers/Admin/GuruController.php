<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Guru::with('subjects')->select('gurus.*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('mapel', fn($row) => $row->subjectNames())
                ->addColumn('status', fn($row) => $row->is_active
            ? '<span class="badge bg-success">Aktif</span>'
            : '<span class="badge bg-secondary">Nonaktif</span>')
                ->addColumn('action', function ($row) {
                $btn = '<div class="d-flex gap-1 justify-content-center">';
                $btn .= '<button type="button" class="btn btn-sm btn-primary btn-edit" ' .
                        'data-id="' . $row->id . '" ' .
                        'data-name="' . htmlspecialchars($row->name ?? '') . '" ' .
                        'data-nip="' . htmlspecialchars($row->nip ?? '') . '" ' .
                        'data-username="' . htmlspecialchars($row->username ?? '') . '" ' .
                        'data-email="' . htmlspecialchars($row->email ?? '') . '" ' .
                        'data-phone="' . htmlspecialchars($row->phone ?? '') . '" ' .
                        'data-is_active="' . $row->is_active . '" title="Edit" data-bs-toggle="tooltip"><i data-feather="edit-2"></i></button>';
                $btn .= '<a href="' . route('admin.guru.subjects', $row->id) . '" class="btn btn-sm btn-info text-white" title="Mapel" data-bs-toggle="tooltip"><i data-feather="book-open"></i></a>';
                $btn .= '<form action="' . route('admin.guru.destroy', $row->id) . '" method="POST" class="d-inline form-delete">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button type="button" class="btn btn-sm btn-danger btn-delete" title="Hapus" data-bs-toggle="tooltip"><i data-feather="trash-2"></i></button>
                             </form>';
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.guru.index');
    }

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:30|unique:gurus',
            'username' => 'required|string|max:50|unique:gurus',
            'email' => 'nullable|email|unique:gurus',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['password', 'photo']);
        $data['password'] = Hash::make($request->password);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('guru-photos', 'public');
        }

        Guru::create($data);

        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $guru = Guru::findOrFail($id);
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:30|unique:gurus,nip,' . $guru->id,
            'username' => 'required|string|max:50|unique:gurus,username,' . $guru->id,
            'email' => 'nullable|email|unique:gurus,email,' . $guru->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'photo' => 'nullable|image|max:2048',
        ]);

        $data = $request->except(['password', 'photo', '_method', '_token']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($guru->photo)
                Storage::disk('public')->delete($guru->photo);
            $data['photo'] = $request->file('photo')->store('guru-photos', 'public');
        }

        $guru->update($data);

        return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        if ($guru->photo)
            Storage::disk('public')->delete($guru->photo);
        $guru->delete();
        return redirect()->route('admin.guru.index')->with('success', 'Guru berhasil dihapus.');
    }

    /** Halaman mapping guru → mata pelajaran */
    public function subjects($id)
    {
        $guru = Guru::with('subjects')->findOrFail($id);
        $subjects = Subject::orderBy('name')->get();
        return view('admin.guru.subjects', compact('guru', 'subjects'));
    }

    /** Simpan mapping guru → mata pelajaran */
    public function syncSubjects(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);
        $guru->subjects()->sync($request->input('subject_ids', []));
        return redirect()->route('admin.guru.subjects', $id)->with('success', 'Mapping mata pelajaran berhasil disimpan.');
    }
}
