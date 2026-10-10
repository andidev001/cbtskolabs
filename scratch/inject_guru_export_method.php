<?php
$content = file_get_contents('app/Http/Controllers/Guru/ProcessingController.php');
$addition = <<<'EOD'
    public function hasilExportExcel(Request $request, $scheduleId)
    {
        $schedule = $this->mySchedules()->findOrFail($scheduleId);
        $fileName = 'Hasil_Ujian_' . str_replace(' ', '_', $schedule->name) . '_' . date('YmdHis') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExamResultExport($scheduleId, $request->group_id), $fileName);
    }
EOD;
$content = preg_replace('/return view\(\'guru\.processing\.hasil-show\', compact\(\'schedule\', \'results\'\)\);\n    }/', "return view('guru.processing.hasil-show', compact('schedule', 'results'));\n    }\n\n" . $addition, $content);
file_put_contents('app/Http/Controllers/Guru/ProcessingController.php', $content);
echo "Done";

