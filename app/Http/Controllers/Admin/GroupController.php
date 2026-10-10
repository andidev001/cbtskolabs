<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Jurusan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Group::withCount('siswas');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<a href="' . route('admin.group.show', $row->id) . '" class="btn btn-sm btn-info text-white me-1" title="Detail" data-bs-toggle="tooltip"><i class="align-middle" data-feather="eye"></i></a>';
                $btn .= '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->name) . '" title="Edit" data-bs-toggle="tooltip"><i class="align-middle" data-feather="edit-2"></i></button>';
                $btn .= '<form action="' . route('admin.group.destroy', $row->id) . '" method="POST" class="d-inline">
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
        return view('admin.group.index');
    }

    /**
     * Halaman detail grup + daftar anggota
     */
    public function show($id)
    {
        $group = Group::findOrFail($id);
        $members = Siswa::where('group_id', $id)->with('jurusan')->orderBy('name')->get();
        $jurusans = Jurusan::all();
        return view('admin.group.show', compact('group', 'members', 'jurusans'));
    }

    /**
     * Tambah anggota (siswa) langsung ke dalam grup ini
     */
    public function storeMember(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:siswas,username',
            'nisn' => 'nullable|string|max:20|unique:siswas,nisn',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'password' => 'required|string|min:4|confirmed',
        ]);

        // Auto-generate nomer_ujian
        $latestNo = Siswa::max('nomer_ujian') ?? '000';
        $newNo = str_pad((int)$latestNo + 1, 5, '0', STR_PAD_LEFT);

        Siswa::create([
            'nomer_ujian' => $newNo,
            'name' => $request->name,
            'username' => $request->username,
            'nisn' => $request->nisn,
            'jurusan_id' => $request->jurusan_id,
            'group_id' => $group->id,
            'password' => Hash::make($request->password),
            'status' => 1,
        ]);

        return redirect()
            ->route('admin.group.show', $group->id)
            ->with('success', 'Peserta berhasil ditambahkan.');
    }

    /**
     * Keluarkan (hapus) siswa dari grup ini, atau hapus akunnya
     */
    public function destroyMember(Request $request, $groupId, $siswaId)
    {
        $siswa = Siswa::findOrFail($siswaId);

        if ($request->action === 'delete') {
            $siswa->delete();
            $msg = 'Akun peserta berhasil dihapus.';
        }
        else {
            // Hanya keluarkan dari grup
            $siswa->group_id = null;
            $siswa->save();
            $msg = 'Peserta berhasil dikeluarkan dari grup.';
        }

        return redirect()
            ->route('admin.group.show', $groupId)
            ->with('success', $msg);
    }


    public function create()
    {
        return view('admin.group.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:groups',
        ]);

        Group::create($request->all());

        return redirect()->route('admin.group.index')->with('success', 'Grup berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);
        return view('admin.group.edit', compact('group'));
    }

    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
        ]);

        $group->update($request->all());

        return redirect()->route('admin.group.index')->with('success', 'Grup berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        // Check if group has students before deleting (optional but good)
        if ($group->siswas()->count() > 0) {
            return back()->with('error', 'Grup tidak bisa dihapus karena masih memiliki peserta.');
        }
        $group->delete();
        return redirect()->route('admin.group.index')->with('success', 'Grup berhasil dihapus.');
    }
}
