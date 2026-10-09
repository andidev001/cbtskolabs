<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Http/Controllers/Admin'));
foreach($files as $f) {
    if(pathinfo($f, PATHINFO_EXTENSION) == 'php') {
        $c = file_get_contents($f);
        $nc = preg_replace('/onclick="return confirm\([^)]+\)"/', 'class="btn-delete"', $c);
        if($c !== $nc) {
            file_put_contents($f, $nc);
            echo "Updated $f\n";
        }
    }
}
