<?php
$files = [
    'resources/views/guru/monitoring/show.blade.php',
    'resources/views/admin/monitoring/show.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Remove the button
    $btnHtml = <<<HTML
                        <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalBeritaAcara">
                            <i data-feather="printer" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Berita Acara
                        </button>
HTML;
    $content = str_replace($btnHtml, '', $content);
    
    // Remove the modal
    $content = preg_replace('/<!-- Modal Berita Acara -->.*?<div class="modal fade" id="modalBeritaAcara".*?<\/div>\s*<\/div>\s*<\/div>/s', '', $content);
    
    file_put_contents($file, $content);
    echo "$file cleaned.\n";
}

