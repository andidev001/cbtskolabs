<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Tangani TokenMismatchException (419 Page Expired) secara elegan
        $this->renderable(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi Anda telah berakhir. Silakan muat ulang halaman.',
                ], 419);
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return redirect()->route('admin.login')->with('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            if ($request->is('guru') || $request->is('guru/*')) {
                return redirect()->route('guru.login')->with('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            if ($request->is('pengawas') || $request->is('pengawas/*')) {
                return redirect()->route('pengawas.login')->with('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            return redirect()->route('siswa.login')->with('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
        });
    }
}
