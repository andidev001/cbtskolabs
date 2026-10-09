<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth('admin')->user();
        return view('admin.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth('admin')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:admins,username,' . $user->id,
            'email' => 'required|email|unique:admins,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($user->photo && file_exists(public_path($user->photo))) {
                unlink(public_path($user->photo));
            }

            $file = $request->file('photo');
            $filename = 'admin_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $data['photo'] = 'uploads/profile/' . $filename;
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
