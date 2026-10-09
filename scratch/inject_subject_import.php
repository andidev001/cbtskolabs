<?php
$content = file_get_contents('app/Http/Controllers/Admin/SubjectController.php');

$imports = "use App\Imports\SubjectImport;\nuse Maatwebsite\Excel\Facades\Excel;";
$content = str_replace("use App\Models\Subject;", "use App\Models\Subject;\n" . $imports, $content);

$addition = <<<'EOD'
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new SubjectImport, $request->file('file'));
            return redirect()->route('admin.subject.index')->with('success', 'Mata Pelajaran berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Gagal import: ' . implode('<br>', $messages));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return response()->streamDownload(function () {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set header
            $sheet->setCellValue('A1', 'nama');
            $sheet->setCellValue('B1', 'kode');
            
            // Set dummy data
            $sheet->setCellValue('A2', 'Matematika');
            $sheet->setCellValue('B2', 'MTK');
            $sheet->setCellValue('A3', 'Bahasa Indonesia');
            $sheet->setCellValue('B3', 'BIN');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Template_Import_Mapel.xlsx');
    }
EOD;

$content = preg_replace('/}([\s\r\n]*)$/', "\n" . $addition . "\n}\n", $content);
file_put_contents('app/Http/Controllers/Admin/SubjectController.php', $content);
echo "Done";

