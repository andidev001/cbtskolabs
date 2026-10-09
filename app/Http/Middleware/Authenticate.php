<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return route('admin.login');
        }

        if ($request->is('guru') || $request->is('guru/*')) {
            return route('guru.login');
        }

        if ($request->is('siswa') || $request->is('siswa/*')) {
            return route('siswa.login');
        }

        return route('login');
    }
}
