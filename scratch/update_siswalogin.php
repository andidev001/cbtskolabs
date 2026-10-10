<?php
$file = 'app/Http/Controllers/Auth/SiswaLoginController.php';
$content = file_get_contents($file);

$old = <<<'EOD'
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('siswa')->attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            return redirect()->intended(route('siswa.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('siswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login');
    }
EOD;

$new = <<<'EOD'
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $siswa = \App\Models\Siswa::where('username', $request->username)->first();
        if ($siswa && \Illuminate\Support\Facades\Hash::check($request->password, $siswa->password)) {
            if ($siswa->is_logged_in) {
                return back()->withErrors([
                    'username' => 'Akun Anda sedang aktif di perangkat lain. Silakan hubungi pengawas untuk mereset login.',
                ])->withInput($request->only('username', 'remember'));
            }

            Auth::guard('siswa')->login($siswa, $request->remember);
            $siswa->update([
                'is_logged_in' => true,
                'session_id' => session()->getId()
            ]);
            
            return redirect()->intended(route('siswa.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username', 'remember'));
    }

    public function logout(Request $request)
    {
        if (Auth::guard('siswa')->check()) {
            Auth::guard('siswa')->user()->update([
                'is_logged_in' => false,
                'session_id' => null
            ]);
        }
        
        Auth::guard('siswa')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('siswa.login');
    }
EOD;

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Updated SiswaLoginController.\n";

