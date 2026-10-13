<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SiswaLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('siswa')->check()) {
            return redirect()->route('siswa.dashboard');
        }
        return view('auth.siswa-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $siswa = Siswa::where('username', $request->username)->first();

        if ($siswa && Hash::check($request->password, $siswa->password)) {
            // Cek apakah siswa terblokir karena kecurangan ujian (buka tab/aplikasi lain)
            $blockedExam = ExamResult::where('siswa_id', $siswa->id)
                ->where('status', 'blocked')
                ->first();

            if ($blockedExam) {
                $reasonText = '';
                try {
                    if (!empty($blockedExam->block_reason)) {
                        $reasonText = ' (' . $blockedExam->block_reason . ')';
                    }
                } catch (\Throwable $e) {}

                return back()->with('error', 'Login ditolak! Akun Anda terkunci karena pelanggaran saat ujian' . $reasonText . '. Silakan hubungi Pengawas untuk membuka blokir.')
                    ->withInput($request->only('username', 'remember'));
            }

            Auth::guard('siswa')->login($siswa, $request->boolean('remember'));
            $siswa->update([
                'is_logged_in' => true,
                'session_id' => session()->getId(),
            ]);

            $request->session()->regenerate();
            return redirect()->route('siswa.dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau Password salah.',
        ])->withInput($request->only('username', 'remember'));
    }

    public function logout(Request $request)
    {
        $siswa = Auth::guard('siswa')->user();
        if ($siswa) {
            $siswa->update([
                'is_logged_in' => false,
                'session_id' => null,
            ]);
        }
        Auth::guard('siswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('siswa.login');
    }
}
