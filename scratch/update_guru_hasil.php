<?php
$file = 'app/Http/Controllers/Guru/ProcessingController.php';
$content = file_get_contents($file);

$old = <<<'EOD'
    public function hasilShow(int $scheduleId)
    {
        $schedule = $this->mySchedules()
            ->with(['examEvent', 'bankSoal'])
            ->findOrFail($scheduleId);

        $results = ExamResult::with(['siswa.group'])
            ->where('exam_schedule_id', $scheduleId)
            ->orderByDesc('score')
            ->get();
EOD;

$new = <<<'EOD'
    public function hasilShow(int $scheduleId)
    {
        $schedule = $this->mySchedules()
            ->with(['examEvent', 'bankSoal'])
            ->findOrFail($scheduleId);

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

        $results = ExamResult::with(['siswa.group'])
            ->where('exam_schedule_id', $scheduleId)
            ->orderByDesc('score')
            ->get();
EOD;

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Updated Guru ProcessingController.\n";

