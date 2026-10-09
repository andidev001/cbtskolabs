<?php
$content = file_get_contents('app/Http/Controllers/Admin/GuruController.php');
$addition = <<<'EOD'
    public function importView()
    {
        return view('admin.guru.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            Excel::import(new GuruImport, $request->file('file'));
            return redirect()->route('admin.guru.index')->with('success', 'Data guru berhasil diimport.');
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
            $sheet->setCellValue('B1', 'nip');
            $sheet->setCellValue('C1', 'username');
            $sheet->setCellValue('D1', 'email');
            $sheet->setCellValue('E1', 'telepon');
            $sheet->setCellValue('F1', 'password');
            
            // Set dummy data
            $sheet->setCellValue('A2', 'Andi Developer');
            $sheet->setCellValue('B2', '199001012023011001');
            $sheet->setCellValue('C2', 'andidev');
            $sheet->setCellValue('D2', 'andi@example.com');
            $sheet->setCellValue('E2', '081234567890');
            $sheet->setCellValue('F2', 'password123');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'Template_Import_Guru.xlsx');
    }
EOD;
$content = preg_replace('/public function subjects\(/', $addition . "\n\n    public function subjects(", $content);
file_put_contents('app/Http/Controllers/Admin/GuruController.php', $content);
echo "Done";

