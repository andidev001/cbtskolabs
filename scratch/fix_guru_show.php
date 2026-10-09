<?php
$file = 'c:/laragon/www/digti-cbt/resources/views/guru/bank-soal/show.blade.php';
$content = file_get_contents($file);
$search = "type' => 'st']) }}\">Setuju / Tidak</a>";
$replace = "type' => 'st']) }}\">Setuju / Tidak</a>\n                                <div class=\"dropdown-divider\"></div>\n                                <a class=\"dropdown-item\" href=\"{{ route('guru.bank-soal.import-word', \$bankSoal->id) }}\">Import dari Word (Docx)</a>";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Done";

