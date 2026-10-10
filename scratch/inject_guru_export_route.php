<?php
$content = file_get_contents('routes/web.php');
$addition = "            Route::get('processing/hasil/{id}/export-excel', [GuruProcessingController::class, 'hasilExportExcel'])->name('guru.processing.hasil.export-excel');\n";
$content = str_replace("Route::get('processing/hasil/{schedule}', [GuruProcessingController::class , 'hasilShow'])->name('guru.processing.hasil.show');", "Route::get('processing/hasil/{schedule}', [GuruProcessingController::class , 'hasilShow'])->name('guru.processing.hasil.show');\n" . $addition, $content);
file_put_contents('routes/web.php', $content);
echo "Done";

