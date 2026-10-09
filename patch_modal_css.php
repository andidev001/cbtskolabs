<?php
$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

$modal_css = <<<CSS
        /* Strict Modal CSS fallback for Body placement */
        body > .modal { position: fixed !important; top: 0 !important; left: 0 !important; z-index: 10050 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.5) !important; display: none; align-items: center; justify-content: center; }
        body > .modal.show { display: flex !important; }
        body > .modal .modal-dialog { position: relative !important; width: 100% !important; max-width: 500px !important; margin: auto !important; z-index: 10060 !important; }
        body > .modal .modal-content { background: #fff !important; border-radius: 12px !important; box-shadow: 0 10px 50px rgba(0,0,0,0.2) !important; border: none !important; overflow: hidden !important; width: 100% !important; }
        body > .modal .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1.2rem 1.5rem; border-bottom: 1px solid #f0f2f5; background-color: #fcfcfc; }
        body > .modal .modal-title { margin: 0; font-weight: 700; font-size: 1.1rem; color: #333; }
        body > .modal .btn-close { background: transparent; border: 0; font-size: 1.5rem; line-height: 1; color: #999; cursor: pointer; padding: 0; margin: 0; transition: color 0.2s; }
        body > .modal .btn-close:hover { color: #333; }
        body > .modal .btn-close::before { content: '\\2715'; }
        body > .modal .modal-body { padding: 1.5rem; max-height: calc(100vh - 180px); overflow-y: auto; }
        body > .modal .modal-footer { display: flex; justify-content: flex-end; align-items: center; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid #f0f2f5; background-color: #fcfcfc; }
CSS;

if (strpos($content, '/* Strict Modal CSS fallback') === false) {
    // Append to our existing override CSS block
    $content = str_replace('</style>', "\n" . $modal_css . "\n</style>", $content);
    file_put_contents($file, $content);
    echo "Modal CSS injected successfully.\n";
} else {
    // Replace it if it exists
    $content = preg_replace('/\/\* Strict Modal CSS fallback.*?\*\/(.*?)(?=\<\/style\>)/s', $modal_css . "\n", $content);
    file_put_contents($file, $content);
    echo "Modal CSS updated successfully.\n";
}
?>
