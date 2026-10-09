<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedules = \App\Models\ExamSchedule::all();
foreach($schedules as $s) {
    echo 'Schedule ' . $s->name . ' end_at: ' . $s->end_at . "\n";
}
$sessions = \App\Models\ExamSession::all();
foreach($sessions as $sess) {
    echo 'Session ' . $sess->name . ' end_time: ' . $sess->end_time . "\n";
}
