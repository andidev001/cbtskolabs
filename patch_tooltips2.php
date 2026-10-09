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
        $pattern = '/(<(a|button)(?![^>]*\btitle=)(?:.*?)>)\s*(<i[^>]*data-feather="' . $icon . '"[^>]*><\/i>)(.*?)(<\/\2>)/is';
        $content = preg_replace_callback($pattern, function($matches) use ($title) {
            $openingTag = $matches[1];
            
            // if it already has title= inside it (maybe missed by negative lookahead due to .*?), don't add.
            if (strpos($openingTag, 'title=') === false) {
                $openingTag = preg_replace('/(>)$/', ' title="' . $title . '" data-bs-toggle="tooltip"$1', $openingTag);
            }
            
            return $openingTag . $matches[3] . $matches[5];
        }, $content);
    }
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated $path\n";
    }
}
