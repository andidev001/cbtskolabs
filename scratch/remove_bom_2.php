<?php
$files = [
    "c:/laragon/www/digti-cbt/resources/views/guru/question/create.blade.php",
    "c:/laragon/www/digti-cbt/resources/views/guru/question/edit.blade.php",
    "c:/laragon/www/digti-cbt/resources/views/admin/question/create.blade.php",
    "c:/laragon/www/digti-cbt/resources/views/admin/question/edit.blade.php"
];
foreach($files as $f) {
    if(file_exists($f)) {
        file_put_contents($f, preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($f)));
    }
}
echo "Done";

