<?php
$content = file_get_contents('routes/web.php');
$addition = <<<'EOD'
            // Subject Import
            Route::post('subject/import', [SubjectController::class, 'import'])->name('admin.subject.import');
            Route::get('subject/template', [SubjectController::class, 'downloadTemplate'])->name('admin.subject.template');
EOD;
$content = str_replace("// Subject Management", $addition . "\n\n            // Subject Management", $content);
file_put_contents('routes/web.php', $content);
echo "Done";

