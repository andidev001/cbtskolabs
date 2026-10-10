<?php
$content = file_get_contents('app/Http/Controllers/Admin/ExamMonitoringController.php');
$addition = <<<CODE
    public function beritaAcara(\Illuminate\Http\Request \$request, \$id)
    {
        \$schedule = \App\Models\ExamSchedule::with(['bankSoal', 'examEvent', 'groups'])->findOrFail(\$id);
        
        \$assignedGroupIds = \$schedule->groups->pluck('id')->toArray();
        \$query = \App\Models\Siswa::whereIn('group_id', \$assignedGroupIds);
        
        if (\$request->has('ruang') && \$request->ruang != '') {
            \$query->where('ruang', \$request->ruang);
        }
        if (\$request->has('sesi') && \$request->sesi != '') {
            \$query->where('sesi', \$request->sesi);
        }
        
        \$students = \$query->orderBy('name')->get();
        \$total_students = \$students->count();
        
        \$results = \App\Models\ExamResult::where('exam_schedule_id', \$id)
            ->whereIn('siswa_id', \$students->pluck('id'))
            ->get()->keyBy('siswa_id');
            
        \$total_present = \$results->count();
        \$total_absent = \$total_students - \$total_present;
        
        \$absent_students = [];
        foreach (\$students as \$student) {
            if (!\$results->has(\$student->id)) {
                \$absent_students[] = \$student->name;
            }
        }

        return view('shared.berita-acara', compact(
            'schedule', 'total_students', 'total_present', 'total_absent', 'absent_students'
        ));
    }
}
CODE;
$content = preg_replace('/return redirect\(\)->back\(\)->with\(\'error\', \'Status siswa bukan terblokir\.\'\);\s*}\s*}/', "return redirect()->back()->with('error', 'Status siswa bukan terblokir.');\n    }\n\n$addition", $content);
file_put_contents('app/Http/Controllers/Admin/ExamMonitoringController.php', $content);
echo "Admin done.\n";

$content = file_get_contents('app/Http/Controllers/Guru/ExamMonitoringController.php');
$content = preg_replace('/return redirect\(\)->back\(\)->with\(\'error\', \'Status siswa bukan terblokir\.\'\);\s*}\s*}/', "return redirect()->back()->with('error', 'Status siswa bukan terblokir.');\n    }\n\n$addition", $content);
file_put_contents('app/Http/Controllers/Guru/ExamMonitoringController.php', $content);
echo "Guru done.\n";
