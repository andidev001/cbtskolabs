<?php
$f = 'c:/laragon/www/digti-cbt/app/Http/Controllers/Guru/BankSoalController.php';
file_put_contents($f, preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($f)));
echo "BOM removed from Guru/BankSoalController\n";

$f2 = 'c:/laragon/www/digti-cbt/routes/web.php';
file_put_contents($f2, preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($f2)));
echo "BOM removed from routes\n";

$f3 = 'c:/laragon/www/digti-cbt/resources/views/guru/bank-soal/show.blade.php';
file_put_contents($f3, preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($f3)));
echo "BOM removed from show blade\n";

$f4 = 'c:/laragon/www/digti-cbt/resources/views/guru/bank-soal/import.blade.php';
file_put_contents($f4, preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($f4)));
echo "BOM removed from import blade\n";

