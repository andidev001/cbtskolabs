<?php
$content = file_get_contents('app/Http/Controllers/Admin/ExamMonitoringController.php');

$addition = <<<'EOD'
    public function show(Request $request, $id)
    {
        $schedule = ExamSchedule::with(['bankSoal', 'groups'])->findOrFail($id);
        
        $totalQuestions = Question::where('bank_soal_id', $schedule->bank_soal_id)->count();
        $assignedGroupIds = $schedule->groups->pluck('id')->toArray();

        if ($request->ajax()) {
            $query = Siswa::whereIn('group_id', $assignedGroupIds)->with(['group']);
            
            if ($request->has('group_id') && $request->group_id != '') {
                $query->where('group_id', $request->group_id);
            }

            $students = $query->orderBy('name')->get();
            $results = ExamResult::where('exam_schedule_id', $id)->get()->keyBy('siswa_id');
            $answerCounts = ExamAnswer::where('exam_schedule_id', $id)
                ->select('siswa_id', DB::raw('count(*) as total'))
                ->groupBy('siswa_id')
                ->get()
                ->keyBy('siswa_id');

            return \Yajra\DataTables\Facades\DataTables::of($students)
                ->addIndexColumn()
                ->addColumn('nomer_ujian', function($row) {
                    return $row->nomer_ujian;
                })
                ->addColumn('name', function($row) {
                    return $row->name;
                })
                ->addColumn('group_name', function($row) {
                    return $row->group->name ?? '-';
                })
                ->addColumn('status', function($row) use ($results) {
                    $result = $results->get($row->id);
                    if (!$result) {
                        return '<span class="badge bg-secondary">Belum Mulai</span>';
                    }
                    if ($result->status === 'blocked') {
                        return '<span class="badge bg-danger">Diblokir</span>';
                    }
                    if ($result->status === 'finished' || $result->finished_at) {
                        return '<span class="badge bg-success">Selesai</span>';
                    }
                    $html = '<span class="badge bg-primary">Sedang Mengerjakan</span>';
                    $html .= '<div class="small text-muted mt-1" style="font-size: 0.7rem;">Mulai: ' . $result->started_at->format('H:i') . '</div>';
                    return $html;
                })
                ->addColumn('progress', function($row) use ($results, $answerCounts, $totalQuestions) {
                    $answerCount = $answerCounts->get($row->id)->total ?? 0;
                    $progress = $totalQuestions > 0 ? round(($answerCount / $totalQuestions) * 100) : 0;
                    $html = '<div class="d-flex align-items-center gap-2">';
                    $html .= '<div class="progress flex-grow-1" style="height: 6px;"><div class="progress-bar" role="progressbar" style="width: '.$progress.'%;" aria-valuenow="'.$progress.'" aria-valuemin="0" aria-valuemax="100"></div></div>';
                    $html .= '<span class="small fw-bold">'.$answerCount.'/'.$totalQuestions.'</span>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('score', function($row) use ($results) {
                    $result = $results->get($row->id);
                    if ($result && $result->finished_at) {
                        return '<span class="text-success fw-bold">' . $result->score . '</span>';
                    }
                    return '<span class="text-muted fw-bold">-</span>';
                })
                ->addColumn('action', function($row) use ($results, $schedule) {
                    $result = $results->get($row->id);
                    $btn = '<div class="d-flex gap-1 justify-content-center">';
                    if ($result && !$result->finished_at && $result->status !== 'blocked') {
                        $url = route('admin.monitoring.force-finish', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST">'.csrf_field().'<button type="submit" class="btn btn-sm btn-warning text-white py-0 px-2" title="Paksa Selesai"><i data-feather="terminal" style="width: 12px; height: 12px;"></i> Selesai</button></form>';
                    }
                    if ($result && $result->status === 'blocked') {
                        $url = route('admin.monitoring.unblock', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST">'.csrf_field().'<button type="submit" class="btn btn-sm btn-success py-0 px-2" onclick="return confirm(\'Buka blokir ujian untuk siswa ini?\')" title="Buka Blokir"><i data-feather="unlock" style="width: 12px; height: 12px;"></i> Buka Blokir</button></form>';
                    }
                    if ($result) {
                        $url = route('admin.monitoring.reset', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST">'.csrf_field().'<button type="submit" class="btn btn-sm btn-danger py-0 px-2" onclick="return confirm(\'Reset ujian siswa ini?\')" title="Reset Total Ujian"><i data-feather="rotate-ccw" style="width: 12px; height: 12px;"></i> Reset Total</button></form>';
                    }
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['status', 'progress', 'score', 'action'])
                ->make(true);
        }
        
        $groups = \App\Models\Group::whereIn('id', $assignedGroupIds)->orderBy('name')->get();

        return view('admin.monitoring.show', compact('schedule', 'groups', 'totalQuestions'));
    }
EOD;

$content = preg_replace('/public function show\(\$id\)\s*\{[\s\S]*?return view\(\'admin\.monitoring\.show\', compact\(\'schedule\', \'students\', \'results\', \'answerCounts\', \'totalQuestions\'\)\);\s*\}/', $addition, $content);
file_put_contents('app/Http/Controllers/Admin/ExamMonitoringController.php', $content);
echo "Done";

