<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('guru')->check()) {
            return redirect()->route('guru.dashboard');
        }
        return view('auth.guru-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('guru')->attempt(['username' => $request->username, 'password' => $request->password], $request->remember)) {
            return redirect()->intended(route('guru.dashboard'));
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('username', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::guard('guru')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('guru.login');
    }
}
