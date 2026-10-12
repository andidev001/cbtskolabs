<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Religion;
use App\Models\Jurusan;
use App\Models\Group;
use App\Imports\SiswaImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Yajra\DataTables\Facades\DataTables;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Siswa::with(['religion', 'jurusan', 'group'])->select('siswas.*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('group_name', function ($row) {
                return $row->group->name ?? '-';
            })
                ->addColumn('jurusan_name', function ($row) {
                return $row->jurusan->name ?? '-';
            })
                ->addColumn('status_label', function ($row) {
                return $row->status ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Non-Aktif</span>';
            })
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center d-flex justify-content-center align-items-center gap-1">';
                $btn .= '<button type="button" class="btn btn-sm btn-warning text-dark btn-view" data-nomer="'.htmlspecialchars($row->nomer_ujian ?? '').'" data-name="'.htmlspecialchars($row->name ?? '').'" data-username="'.htmlspecialchars($row->username ?? '').'" data-password="'.htmlspecialchars($row->password_raw ?? '').'" data-religion="'.htmlspecialchars($row->religion->name ?? '-').'" data-jurusan="'.htmlspecialchars($row->jurusan->name ?? '-').'" data-group="'.htmlspecialchars($row->group->name ?? '-').'" data-sesi="'.htmlspecialchars($row->sesi ?? '').'" data-status="'.htmlspecialchars($row->status ? 'Aktif' : 'Non-Aktif').'" title="Lihat Detail" data-bs-toggle="tooltip"><i class="align-middle" data-feather="eye"></i></button>';
                $btn .= '<button type="button" class="btn btn-sm btn-info text-white btn-edit" data-id="'.htmlspecialchars($row->id ?? '').'" data-nomer="'.htmlspecialchars($row->nomer_ujian ?? '').'" data-name="'.htmlspecialchars($row->name ?? '').'" data-username="'.htmlspecialchars($row->username ?? '').'" data-religion="'.htmlspecialchars($row->religion_id ?? '').'" data-jurusan="'.htmlspecialchars($row->jurusan_id ?? '').'" data-group="'.htmlspecialchars($row->group_id ?? '').'" data-sesi="'.htmlspecialchars($row->sesi ?? '').'" data-status="'.htmlspecialchars($row->status ?? '').'" title="Edit" data-bs-toggle="tooltip"><i class="align-middle" data-feather="edit-2"></i></button>';
                $btn .= '<form action="' . route('admin.peserta.destroy', $row->id) . '" method="POST" class="d-inline mb-0">
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
        $religions = Religion::all();
        $jurusans = Jurusan::all();
        $groups = Group::all();
        return view('admin.siswa.index', compact('religions', 'jurusans', 'groups'));
    }

    public function create()
    {
        $religions = Religion::all();
        $jurusans = Jurusan::all();
        $groups = Group::all();
        return view('admin.siswa.create', compact('religions', 'jurusans', 'groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomer_ujian' => 'required|unique:siswas',
            'name' => 'required|string|max:255',
            'username' => 'required|unique:siswas',
            'password' => 'required|min:6',
            'religion_id' => 'nullable|exists:religions,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'group_id' => 'nullable|exists:groups,id',
            'sesi' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['password_raw'] = $request->password;

        Siswa::create($data);

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $religions = Religion::all();
        $jurusans = Jurusan::all();
        $groups = Group::all();
        return view('admin.siswa.edit', compact('siswa', 'religions', 'jurusans', 'groups'));
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $request->validate([
            'nomer_ujian' => 'required|unique:siswas,nomer_ujian,' . $siswa->id,
            'name' => 'required|string|max:255',
            'username' => 'required|unique:siswas,username,' . $siswa->id,
            'password' => 'nullable|min:6',
            'religion_id' => 'nullable|exists:religions,id',
            'jurusan_id' => 'nullable|exists:jurusans,id',
            'group_id' => 'nullable|exists:groups,id',
            'sesi' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['password_raw'] = $request->password;
        }
        else {
            unset($data['password']);
            unset($data['password_raw']);
        }

        $siswa->update($data);

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $siswa->delete();
        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil dihapus.');
    }

    public function exportTemplate()
    {
        return Excel::download(new \App\Exports\SiswaTemplateExport, 'template_import_peserta.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data peserta berhasil diimport.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal mengimport data peserta: ' . $e->getMessage());
        }
    }

    public function cardPreview(Request $request)
    {
        $groups = Group::all();
        $jurusans = Jurusan::all();

        $query = Siswa::with(['group', 'jurusan']);

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $siswas = $query->paginate(8);
        $title = $request->title ?? 'UJIAN SEKOLAH ' . date('Y');
        $setting = \App\Models\Setting::all()->pluck('value', 'key');

        return view('admin.siswa.card_preview', compact('siswas', 'groups', 'jurusans', 'title', 'setting'));
    }

    public function printCards(Request $request)
    {
        $query = Siswa::with(['group', 'jurusan']);

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('jurusan_id')) {
            $query->where('jurusan_id', $request->jurusan_id);
        }

        $siswas = $query->get();
        $title = $request->title ?? 'UJIAN SEKOLAH ' . date('Y');
        $setting = \App\Models\Setting::all()->pluck('value', 'key');

        $pdf = Pdf::loadView('admin.siswa.cards', compact('siswas', 'setting', 'title'))->setPaper('a4', 'portrait');
        return $pdf->stream('kartu_peserta.pdf');
    }

    public function resetLogin($id)
    {
        $siswa = \App\Models\Siswa::findOrFail($id);
        $siswa->update([
            'is_logged_in' => false,
            'session_id' => null
        ]);
        return redirect()->back()->with('success', 'Status login siswa berhasil direset.');
    }
}