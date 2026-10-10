<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExamStatusController extends Controller
{
    private function authorizedSchedule(ExamSchedule $examSchedule)
    {
        // Pengawas can access all active schedules
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExamSchedule::with(['examEvent', 'bankSoal'])
                ->where('is_active', true)
                ->whereDate('start_at', '<=', now()->toDateString())
                ->whereDate('end_at', '>=', now()->toDateString())
                ->select('exam_schedules.*')
                ->get();
            foreach ($data as $item) {
                $item->maybeRefreshToken();
            }

            return \Yajra\DataTables\Facades\DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('event_name', fn($row) => $row->examEvent->name ?? '-')
                ->addColumn('bank_soal_name', fn($row) => $row->bankSoal->name ?? '-')
                ->addColumn('token_label', function ($row) {
                if ($row->token) {
                    return '<div class="text-center">
                                    <h4 class="fw-bold text-primary mb-0" style="letter-spacing: 2px;">' . $row->token . '</h4>
                                    <small class="text-muted" style="font-size: 0.6rem;">Refreshed: ' . ($row->token_refreshed_at ? $row->token_refreshed_at->format('H:i:s') : '-') . '</small>
                                </div>';
                }
                return '<div class="text-center text-muted small"><em>No Token</em></div>';
            })
                ->addColumn('action', function ($row) {
                $btn = '<div class="text-center">';
                $btn .= '<form action="' . route('pengawas.exam-status.release-token', $row->id) . '" method="POST" class="d-inline form-release-token">
                                    ' . csrf_field() . '
                                    <button type="submit" class="btn btn-sm btn-primary me-1 btn-release-token" title="Rilis Token Baru"><i class="align-middle" data-feather="refresh-cw"></i></button>
                                </form>';
                if ($row->token) {
                    $btn .= '<form action="' . route('pengawas.exam-status.clear-token', $row->id) . '" method="POST" class="d-inline form-clear-token">
                                        ' . csrf_field() . '
                                        <button type="submit" class="btn btn-sm btn-danger btn-clear-token" title="Hapus Token"><i class="align-middle" data-feather="x-circle"></i></button>
                                    </form>';
                }
                $btn .= '</div>';
                return $btn;
            })
                ->rawColumns(['token_label', 'action'])
                ->make(true);
        }

        return view('pengawas.exam-status.index');
    }

    public function releaseToken(Request $request, $id)
    {
        $schedule = ExamSchedule::findOrFail($id);
        $this->authorizedSchedule($schedule);

        // Generate a 6-character uppercase random string
        $token = strtoupper(Str::random(6));

        $schedule->update([
            'token' => $token,
            'token_refreshed_at' => now()
        ]);

        return redirect()->back()->with('success', "Token baru berhasil dirilis: **$token**");
    }

    public function clearToken($id)
    {
        $schedule = ExamSchedule::findOrFail($id);
        $this->authorizedSchedule($schedule);
        $schedule->update([
            'token' => null,
            'token_refreshed_at' => null
        ]);

        return redirect()->back()->with('success', 'Token berhasil dinonaktifkan.');
    }

    public function updateInterval(Request $request, $id)
    {
        $request->validate([
            'token_interval' => 'nullable|integer|min:0'
        ]);

        $schedule = ExamSchedule::findOrFail($id);
        $this->authorizedSchedule($schedule);
        $schedule->update([
            'token_interval' => $request->token_interval ?: null
        ]);

        return redirect()->back()->with('success', 'Interval ganti token berhasil diperbarui.');
    }
}
