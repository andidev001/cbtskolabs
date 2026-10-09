<?php
$adminController = file_get_contents('c:/laragon/www/digti-cbt/app/Http/Controllers/Admin/BankSoalController.php');
$guruController = file_get_contents('c:/laragon/www/digti-cbt/app/Http/Controllers/Guru/BankSoalController.php');

// Extract importWord, processImportWord, downloadTemplateWord
$startStr = 'public function importWord';
$endStr = 'public function destroy(string $id)';
preg_match('/' . preg_quote($startStr, '/') . '(.*?)' . preg_quote($endStr, '/') . '/s', $adminController, $matches);

if(isset($matches[0])) {
    $methodsCode = $matches[0];
    
    // Replace admin. with guru.
    $methodsCode = str_replace("route('admin.", "route('guru.", $methodsCode);
    
    // Add auth check logic to ensure Guru can only import to their own bank_soal
    // Admin uses BankSoal::findOrFail. We should use auth()->user()->subjects check, but $bankSoal is already fetched.
    $authCheck = "
        \$guru = \Illuminate\Support\Facades\Auth::guard('guru')->user();
        \$subjectIds = \$guru->subjects()->pluck('subjects.id')->toArray();
        if(!in_array(\$bankSoal->subject_id, \$subjectIds)) abort(403);
";
    $methodsCode = str_replace("\$bankSoal = BankSoal::findOrFail(\$bankSoalId);", "\$bankSoal = BankSoal::findOrFail(\$bankSoalId);" . $authCheck, $methodsCode);

    // Remove the trailing `public function destroy` which we caught as boundary
    $methodsCode = preg_replace('/public function destroy.*$/s', '', $methodsCode);
    
    // Insert into Guru\BankSoalController before the final brace
    $guruController = preg_replace('/}\s*$/', "\n" . $methodsCode . "\n}", $guruController);
    
    // Also we need to make sure IOFactory and PhpWord are imported in Guru
    if(strpos($guruController, 'use PhpOffice\PhpWord\IOFactory;') === false) {
        $guruController = str_replace("use Illuminate\Http\Request;", "use Illuminate\Http\Request;\nuse PhpOffice\PhpWord\PhpWord;\nuse PhpOffice\PhpWord\IOFactory;\nuse PhpOffice\PhpWord\SimpleType\Jc;", $guruController);
    }
    
    file_put_contents('c:/laragon/www/digti-cbt/app/Http/Controllers/Guru/BankSoalController.php', $guruController);
    echo "Done patching Guru Controller";
} else {
    echo "Could not find methods in Admin Controller";
}

