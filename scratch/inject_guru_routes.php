<?php
$content = file_get_contents('routes/web.php');
$addition = <<<'EOD'
            // Guru Import
            Route::get('guru/import', [GuruController::class, 'importView'])->name('admin.guru.import.view');
            Route::post('guru/import', [GuruController::class, 'import'])->name('admin.guru.import');
            Route::get('guru/template', [GuruController::class, 'downloadTemplate'])->name('admin.guru.template');
EOD;
$content = str_replace("// Guru Management", $addition . "\n\n            // Guru Management", $content);
file_put_contents('routes/web.php', $content);
echo "Done";

