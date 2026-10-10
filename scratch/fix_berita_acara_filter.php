<?php
$files = [
    'app/Http/Controllers/Admin/BeritaAcaraController.php',
    'app/Http/Controllers/Guru/BeritaAcaraController.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // Pattern to remove
    $pattern = '/\s*if \(\$request->has\(\'ruang\'\).*?\s*\}\s*if \(\$request->has\(\'sesi\'\).*?\s*\}/s';
    
    $content = preg_replace($pattern, '', $content);
    
    file_put_contents($file, $content);
    echo "$file fixed.\n";
}

