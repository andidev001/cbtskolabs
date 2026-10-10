<?php
$file = 'app/Http/Controllers/Admin/ExamProcessingController.php';
$content = file_get_contents($file);

$old = <<<'EOD'
    public function hasilShow(Request $request, $scheduleId)
    {
        $schedule = ExamSchedule::with(['bankSoal', 'examEvent'])->findOrFail($scheduleId);
        
        if ($request->ajax()) {
            $query = ExamResult::where('exam_schedule_id', $scheduleId)
                ->with(['siswa.group'])
                ->select('exam_results.*');
EOD;

$new = <<<'EOD'
    public function hasilShow(Request $request, $scheduleId)
    {
        $schedule = ExamSchedule::with(['bankSoal', 'examEvent'])->findOrFail($scheduleId);
        
        if ($request->ajax()) {
            // Auto-finish exams that are out of time before returning data
            $ongoingResults = ExamResult::where('exam_schedule_id', $scheduleId)
                ->whereNull('finished_at')
                ->where('status', '!=', 'blocked')
                ->get();
            $now = \Carbon\Carbon::now();
            foreach ($ongoingResults as $res) {
                $student = \App\Models\Siswa::find($res->siswa_id);
                $examEnd = \Carbon\Carbon::parse($schedule->end_at);
                if ($student) {
                    $session = \App\Models\ExamSession::where('name', $student->sesi)->first();
                    if ($session) {
                        $sessionEnd = \Carbon\Carbon::createFromFormat('H:i:s', $session->end_time);
                        if ($sessionEnd->lt($examEnd)) {
                            $examEnd = $sessionEnd;
                        }
                    }
                }
                $maxEnd = $res->started_at->copy()->addMinutes($schedule->duration);
                if ($maxEnd->lt($examEnd)) {
                    $examEnd = $maxEnd;
                }
                if ($now->gt($examEnd)) {
                    \App\Models\ExamResult::calculateScoreAndFinish($schedule->id, $res->siswa_id);
                }
            }

            $query = ExamResult::where('exam_schedule_id', $scheduleId)
                ->with(['siswa.group'])
                ->select('exam_results.*');
EOD;

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Updated Admin ExamProcessingController.\n";

