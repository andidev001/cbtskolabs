<?php
$content = file_get_contents('routes/web.php');
$addition = <<<'EOD'
            Route::post('subject/import', [SubjectController::class, 'import'])->name('admin.subject.import');
            Route::get('subject/template', [SubjectController::class, 'downloadTemplate'])->name('admin.subject.template');
            Route::resource('subject', SubjectController::class)
EOD;
$content = str_replace("Route::resource('subject', SubjectController::class)", $addition, $content);
file_put_contents('routes/web.php', $content);
echo "Done";

