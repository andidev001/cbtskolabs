<?php
$content = file_get_contents('app/Models/ExamSchedule.php');
if (strpos($content, 'beritaAcara') === false) {
    $content = preg_replace('/}\s*$/', "\n    public function beritaAcara()\n    {\n        return \$this->hasOne(BeritaAcara::class);\n    }\n}", $content);
    file_put_contents('app/Models/ExamSchedule.php', $content);
}
echo "OK";

