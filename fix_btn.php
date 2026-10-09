<?php
$file = 'resources/views/admin/religion/index.blade.php';
$content = file_get_contents($file);

$btnReplacement = <<<HTML
    <button type="button" class="btn btn-primary" data-bs-target="#createReligionModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Agama
    </button>
HTML;

$content = preg_replace('/<a href="\{\{\s*route\(\'admin\.religion\.create\'\)\s*\}\}".*?>.*?<\/a>/is', $btnReplacement, $content);

file_put_contents($file, $content);
echo "Button fixed.\n";
?>
