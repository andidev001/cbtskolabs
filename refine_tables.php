<?php
$layoutFiles = [
    'resources/views/layouts/admin.blade.php',
    'resources/views/layouts/guru.blade.php'
];

$newStyles = "
    <style>
        /* custom fine ui */
        body { font-size: 0.875rem; background-color: #f5f7fb; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.03) !important; border: none !important; border-radius: 8px !important; margin-bottom: 1.5rem; }
        .card-header { border-bottom: 1px solid #f0f2f5; background-color: #fff; border-radius: 8px 8px 0 0 !important; padding: 1rem 1.25rem; }
        .card-body { padding: 1.25rem; }
        .form-control, .form-select, .btn { font-size: 0.85rem !important; border-radius: 5px !important; }
        
        /* Table & DataTables refinements */
        .table { font-size: 0.8rem; color: #495057; margin-bottom: 0 !important; }
        .table > :not(caption) > * > * { padding: 0.6rem 0.75rem; vertical-align: middle; border-bottom-color: #f0f2f5; }
        .table thead th { font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; background-color: #f8f9fa; color: #6c757d; border-bottom: 2px solid #eef2f7 !important; padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .table tbody tr:hover { background-color: rgba(0,0,0,0.015); }
        
        /* Buttons inside table */
        .table .btn { padding: 0.25rem 0.5rem !important; font-size: 0.75rem !important; line-height: 1.2; margin-bottom: 0.2rem; }
        .table .btn i, .table .btn svg { width: 14px; height: 14px; }
        
        /* DataTables wrapper spacing & fixes */
        .dataTables_wrapper .row { align-items: center; margin-bottom: 0.5rem; }
        div.dataTables_wrapper div.dataTables_length select { width: auto; display: inline-block; padding: 0.25rem 1.5rem 0.25rem 0.5rem; font-size: 0.8rem; border-radius: 4px; }
        div.dataTables_wrapper div.dataTables_filter input { margin-left: 0.5em; display: inline-block; width: auto; padding: 0.25rem 0.5rem; font-size: 0.8rem; border-radius: 4px; }
        div.dataTables_wrapper div.dataTables_info { padding-top: 0.5em; font-size: 0.8rem; color: #888; }
        div.dataTables_wrapper div.dataTables_paginate { margin-top: 0.5em; }
        .pagination { margin-bottom: 0; justify-content: flex-end; }
        .page-item .page-link { font-size: 0.8rem; padding: 0.3rem 0.75rem; border-radius: 4px; margin: 0 2px; border: 1px solid #eef2f7; color: #495057; }
        .page-item.active .page-link { background-color: var(--sidebar-bg, #0d3a85); border-color: var(--sidebar-bg, #0d3a85); color: #fff; }
        
        /* Badges */
        .badge { padding: 0.35em 0.65em; font-weight: 500; border-radius: 4px; }
        
        .sidebar-link { font-size: 0.85rem; padding: 0.65rem 1.5rem !important; }
        .sidebar-header { font-size: 0.65rem !important; padding: 1.2rem 1.5rem 0.5rem !important; }
        .navbar { padding: 0.5rem 1rem !important; min-height: 50px; border-bottom: 1px solid #f0f2f5; box-shadow: none; }
    </style>
</head>";

foreach ($layoutFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        // Remove old style tag entirely if it exists
        if (preg_match('/<style>\s*\/\* custom fine ui \*\/.*?<\/style>\s*<\/head>/s', $content)) {
            $content = preg_replace('/<style>\s*\/\* custom fine ui \*\/.*?<\/style>\s*<\/head>/s', $newStyles, $content);
        } else {
            $content = str_replace('</head>', $newStyles, $content);
        }
        file_put_contents($file, $content);
    }
}
