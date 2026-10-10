<?php
$file = 'app/Http/Controllers/Admin/ExamMonitoringController.php';
$content = file_get_contents($file);

$old = <<<'EOD'
    public function forceFinish($scheduleId, $siswaId)
    {
        $result = ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($result && !$result->finished_at) {
            // Hitung skor saat ini
            $answers = ExamAnswer::where('exam_schedule_id', $scheduleId)
                ->where('siswa_id', $siswaId)
                ->with('question')
                ->get();

            $total = Question::where('bank_soal_id', $result->examSchedule->bank_soal_id)->count();
            $correct = $answers->filter(fn($a) => strtoupper($a->answer ?? '') === strtoupper($a->question->answer ?? ''))->count();
            $score = $total > 0 ? round($correct / $total * 100, 1) : 0;

            $result->update([
                'finished_at' => now(),
                'total_questions' => $total,
                'correct_answers' => $correct,
                'score' => $score
            ]);

            return redirect()->back()->with('success', 'Siswa berhasil dipaksa selesai.');
        }

        return redirect()->back()->with('error', 'Siswa sudah selesai atau belum memulai.');
    }
EOD;

$new = <<<'EOD'
    public function forceFinish($scheduleId, $siswaId)
    {
        $result = ExamResult::where('exam_schedule_id', $scheduleId)
            ->where('siswa_id', $siswaId)
            ->first();

        if ($result && !$result->finished_at) {
            \App\Models\ExamResult::calculateScoreAndFinish($scheduleId, $siswaId);
            return redirect()->back()->with('success', 'Siswa berhasil dipaksa selesai.');
        }

        return redirect()->back()->with('error', 'Siswa sudah selesai atau belum memulai.');
    }
EOD;

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Updated forceFinish.\n";

