<?php
$content = file_get_contents('routes/web.php');

// Add Admin Route
$adminAdd = <<<CODE
            Route::get('monitoring', [ExamMonitoringController::class, 'index'])->name('admin.monitoring.index');
            Route::get('berita-acara', [\App\Http\Controllers\Admin\BeritaAcaraController::class, 'index'])->name('admin.berita-acara.index');
            Route::get('berita-acara/{schedule}/cetak', [\App\Http\Controllers\Admin\BeritaAcaraController::class, 'cetak'])->name('admin.berita-acara.cetak');
CODE;
$content = preg_replace('/Route::get\(\'monitoring\', \[ExamMonitoringController::class, \'index\'\]\)->name\(\'admin\.monitoring\.index\'\);/', $adminAdd, $content);

// Remove old Admin Berita Acara route if exists
$content = preg_replace('/Route::get\(\'monitoring\/\{schedule\}\/berita-acara\', \[ExamMonitoringController::class, \'beritaAcara\'\]\)->name\(\'admin\.monitoring\.berita-acara\'\);\n/', '', $content);

// Add Guru Route
$guruAdd = <<<CODE
            Route::get('monitoring', [\App\Http\Controllers\Guru\ExamMonitoringController::class, 'index'])->name('guru.monitoring.index');
            Route::get('berita-acara', [\App\Http\Controllers\Guru\BeritaAcaraController::class, 'index'])->name('guru.berita-acara.index');
            Route::get('berita-acara/{schedule}/cetak', [\App\Http\Controllers\Guru\BeritaAcaraController::class, 'cetak'])->name('guru.berita-acara.cetak');
CODE;
$content = preg_replace('/Route::get\(\'monitoring\', \[\\\\App\\\\Http\\\\Controllers\\\\Guru\\\\ExamMonitoringController::class, \'index\'\]\)->name\(\'guru\.monitoring\.index\'\);/', $guruAdd, $content);

// Remove old Guru Berita Acara route if exists
$content = preg_replace('/Route::get\(\'monitoring\/\{schedule\}\/berita-acara\', \[\\\\App\\\\Http\\\\Controllers\\\\Guru\\\\ExamMonitoringController::class, \'beritaAcara\'\]\)->name\(\'guru\.monitoring\.berita-acara\'\);\n/', '', $content);


file_put_contents('routes/web.php', $content);
echo "Routes updated.\n";

