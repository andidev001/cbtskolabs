<?php
$loginFiles = [
    'resources/views/auth/siswa-login.blade.php',
    'resources/views/auth/guru-login.blade.php',
    'resources/views/auth/admin-login.blade.php'
];

foreach ($loginFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $content = str_replace('border-radius: 15px;', 'border-radius: 8px;', $content);
        $content = str_replace('box-shadow: 0 10px 30px rgba(0,0,0,0.1);', 'box-shadow: 0 4px 15px rgba(0,0,0,0.05);', $content);
        $content = str_replace('form-control-lg', '', $content);
        $content = str_replace('input-group-lg', '', $content);
        $content = str_replace('btn-lg', '', $content);
        $content = str_replace('width="20" height="20"', 'width="18" height="18"', $content);
        file_put_contents($file, $content);
    }
}

$layoutFiles = [
    'resources/views/layouts/admin.blade.php',
    'resources/views/layouts/guru.blade.php'
];

$styleOverrides = "
    <style>
        /* custom fine ui */
        body { font-size: 0.875rem; }
        .card { box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important; border: 1px solid rgba(0,0,0,0.05) !important; border-radius: 8px !important; }
        .form-control, .form-select, .btn { font-size: 0.875rem !important; border-radius: 6px !important; }
        .table { font-size: 0.85rem; }
        .sidebar-link { font-size: 0.85rem; padding: 0.65rem 1.5rem !important; }
        .sidebar-header { font-size: 0.65rem !important; padding: 1.2rem 1.5rem 0.5rem !important; }
        .navbar { padding: 0.5rem 1rem !important; min-height: 50px; }
    </style>
</head>";

foreach ($layoutFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (strpos($content, '/* custom fine ui */') === false) {
            $content = str_replace('</head>', $styleOverrides, $content);
            file_put_contents($file, $content);
        }
    }
}
