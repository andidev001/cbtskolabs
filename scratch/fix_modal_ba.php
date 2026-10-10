<?php
$files = [
    'resources/views/admin/berita-acara/index.blade.php',
    'resources/views/guru/berita-acara/index.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Replace the modal wrapper
    $content = preg_replace('/<!-- Modal Berita Acara -->\n<div class="modal fade" id="modalBeritaAcara" tabindex="-1" aria-hidden="true">/s', "@push('modals')\n<!-- Modal Berita Acara -->\n<div class=\"modal fade\" id=\"modalBeritaAcara\" tabindex=\"-1\" aria-hidden=\"true\">", $content);
    
    $content = preg_replace('/<\/form>\n        <\/div>\n    <\/div>\n<\/div>\n\n@push\(\'scripts\'\)/s', "</form>\n        </div>\n    </div>\n</div>\n@endpush\n\n@push('scripts')", $content);

    file_put_contents($file, $content);
    echo "$file updated.\n";
}

