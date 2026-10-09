<?php
$directory = new RecursiveDirectoryIterator('app/Http/Controllers');
$iterator = new RecursiveIteratorIterator($directory);
$files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$icons = [
    'edit-2' => 'Edit',
    'trash-2' => 'Hapus',
    'book-open' => 'Mapel',
    'eye' => 'Detail',
    'monitor' => 'Monitoring',
    'settings' => 'Pengaturan',
    'users' => 'Peserta',
    'file-text' => 'Nilai',
    'check-circle' => 'Selesai',
    'play' => 'Mulai',
    'stop-circle' => 'Hentikan',
    'refresh-cw' => 'Reset'
];

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $original = $content;

    foreach ($icons as $icon => $title) {
        $pattern = '/(<(a|button)(?![^>]*\btitle=)[^>]*>)\s*(<i[^>]*data-feather="' . $icon . '"[^>]*><\/i>)(.*?)(<\/\2>)/is';
        $content = preg_replace_callback($pattern, function($matches) use ($title) {
            $openingTag = $matches[1];
            // remove class text-white if any, wait not needed.
            // insert title="Title"
            $openingTag = preg_replace('/>$/', ' title="' . $title . '">', $openingTag);
            // also remove the text like " Edit" from matches 4 if it exists, wait, user wants icon only tooltips?
            // "di data tabel pada semua buton aksi ketika diarahkan ada keterangan misal edit, hapus dst"
            // Let's remove the inner text so it's icon only, and tooltips are active.
            return $openingTag . $matches[3] . $matches[5];
        }, $content);
    }
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated $path\n";
    }
}
