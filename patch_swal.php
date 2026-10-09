<?php
$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

$swal_css = <<<CSS
        /* SweetAlert Tweaks (Smaller & Smoother) */
        .swal2-popup { font-size: 0.85rem !important; padding: 1.5rem !important; width: auto !important; min-width: 300px !important; max-width: 380px !important; border-radius: 12px !important; }
        .swal2-title { font-size: 1.3rem !important; margin-bottom: 0.25rem !important; font-weight: 600 !important; }
        .swal2-html-container { font-size: 0.9rem !important; margin: 0.5rem 0 !important; color: #64748b !important; }
        .swal2-icon { width: 4.5rem !important; height: 4.5rem !important; margin: 1rem auto !important; }
        .swal2-icon .swal2-icon-content { font-size: 2.5rem !important; }
        .swal2-actions { margin-top: 1rem !important; gap: 8px !important; }
        .swal2-styled { padding: 0.4rem 1rem !important; font-size: 0.85rem !important; border-radius: 6px !important; font-weight: 500 !important; }
        .swal2-confirm { box-shadow: 0 4px 6px -1px rgba(220, 53, 69, 0.3) !important; }
        .swal2-cancel { box-shadow: none !important; }
CSS;

if (strpos($content, '/* SweetAlert Tweaks') === false) {
    // Append to our existing override CSS block
    $content = str_replace('</style>', "\n" . $swal_css . "\n</style>", $content);
    file_put_contents($file, $content);
    echo "SweetAlert CSS injected successfully.\n";
} else {
    // Replace it if it exists (for updates)
    $content = preg_replace('/\/\* SweetAlert Tweaks.*?\*\/(.*?)(?=\<\/style\>)/s', $swal_css . "\n", $content);
    file_put_contents($file, $content);
    echo "SweetAlert CSS updated successfully.\n";
}
?>
