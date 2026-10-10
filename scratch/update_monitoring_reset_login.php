<?php
$file = 'app/Http/Controllers/Admin/ExamMonitoringController.php';
$content = file_get_contents($file);

$old = <<<'EOD'
                    if ($result) {
                        $url = route('admin.monitoring.reset', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST" class="form-reset">'.csrf_field().'<button type="button" class="btn btn-sm btn-danger py-0 px-2 btn-reset" title="Reset Total Ujian"><i data-feather="rotate-ccw" style="width: 12px; height: 12px;"></i> Reset Total</button></form>';
                    }
                    $btn .= '</div>';
EOD;

$new = <<<'EOD'
                    if ($result) {
                        $url = route('admin.monitoring.reset', [$schedule->id, $row->id]);
                        $btn .= '<form action="'.$url.'" method="POST" class="form-reset">'.csrf_field().'<button type="button" class="btn btn-sm btn-danger py-0 px-2 btn-reset" title="Reset Total Ujian"><i data-feather="rotate-ccw" style="width: 12px; height: 12px;"></i> Reset Total</button></form>';
                    }
                    
                    $resetLoginUrl = route('admin.peserta.reset-login', $row->id);
                    $btn .= '<form action="'.$resetLoginUrl.'" method="POST" class="form-reset-login d-inline" onsubmit="return confirm(\'Reset login siswa ini?\')">'.csrf_field().'<button type="submit" class="btn btn-sm btn-dark py-0 px-2 btn-reset-login" title="Reset Login"><i data-feather="refresh-cw" style="width: 12px; height: 12px;"></i> Reset Login</button></form>';

                    $btn .= '</div>';
EOD;

if(strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "Updated Monitoring\n";
} else {
    echo "Old not found\n";
}

