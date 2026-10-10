<?php
$files = [
    'resources/views/layouts/admin.blade.php',
    'resources/views/layouts/guru.blade.php'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    $prefix = strpos($file, 'guru') !== false ? 'guru' : 'admin';
    
    // Add to $isUjianActive if not already there
    if (strpos($content, "'$prefix.berita-acara.*'") === false) {
        $content = str_replace("'$prefix.exam-schedule.*'", "'$prefix.exam-schedule.*', '$prefix.berita-acara.*'", $content);
    }

    $search = '<a class="sidebar-link" href="{{ route(\''.$prefix.'.exam-schedule.index\') }}"><i class="align-middle" data-feather="clock"></i> <span class="align-middle">Jadwal Ujian</span></a>
							</li>';
                            
    $replace = $search . '
							<li class="sidebar-item {{ request()->routeIs(\''.$prefix.'.berita-acara.*\') ? \'active\' : \'\' }}">
								<a class="sidebar-link" href="{{ route(\''.$prefix.'.berita-acara.index\') }}"><i class="align-middle" data-feather="printer"></i> <span class="align-middle">Berita Acara</span></a>
							</li>';

    if (strpos($content, 'Berita Acara') === false) {
        $content = str_replace($search, $replace, $content);
    }
    
    file_put_contents($file, $content);
    echo "$file updated.\n";
}

