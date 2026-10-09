<?php
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('app/Http/Controllers/Admin'));
foreach($files as $f) {
    if(pathinfo($f, PATHINFO_EXTENSION) == 'php') {
        $c = file_get_contents($f);
        $nc = preg_replace('/class="btn btn-sm btn-danger"(.*?)class="btn-delete"/', 'class="btn btn-sm btn-danger btn-delete"$1', $c);
        
        // Some might have different spaces or attributes in between
        $nc = str_replace('class="btn btn-sm btn-danger" class="btn-delete"', 'class="btn btn-sm btn-danger btn-delete"', $nc);
        if($c !== $nc) {
            file_put_contents($f, $nc);
            echo "Updated $f\n";
        }
    }
}
