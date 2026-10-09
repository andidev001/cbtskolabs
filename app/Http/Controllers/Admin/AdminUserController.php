<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Admin::select('admins.*');
            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<a href="' . route('admin.user.edit', $row->id) . '" class="btn btn-sm btn-primary me-1" title="Edit" data-bs-toggle="tooltip"><i class="align-middle" data-feather="edit-2"></i></a>';
                $btn .= '<form action="' . route('admin.user.destroy', $row->id) . '" method="POST" class="d-inline">
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
        return view('admin.user.index');
    }

    public function create()
    {
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:admins',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
        ]);

        Admin::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User Admin berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = Admin::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = Admin::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:admins,username,' . $user->id,
            'email' => 'required|email|unique:admins,email,' . $user->id,
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.user.index')->with('success', 'User Admin berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (Admin::count() <= 1) {
            return back()->with('error', 'Tidak bisa menghapus user admin terakhir.');
        }
        $user = Admin::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'User Admin berhasil dihapus.');
    }
}
