<?php
$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

$override_css = <<<CSS
        /* Kekinian Table & DataTables */
        .table { font-size: 0.8rem !important; color: #4a5568 !important; border-collapse: separate !important; border-spacing: 0 !important; }
        .table > :not(caption) > * > * { padding: 0.7rem 1rem !important; vertical-align: middle !important; border-bottom: 1px dashed #e2e8f0 !important; }
        .table thead th { font-weight: 600 !important; font-size: 0.7rem !important; text-transform: uppercase !important; letter-spacing: 0.8px !important; background-color: #f8fafc !important; color: #64748b !important; border-bottom: none !important; border-top: none !important; padding: 0.85rem 1rem !important; }
        .table thead tr th:first-child { border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; }
        .table thead tr th:last-child { border-top-right-radius: 8px !important; border-bottom-right-radius: 8px !important; }
        .table tbody tr:last-child td { border-bottom: none !important; }
        .table tbody tr { transition: all 0.2s ease !important; }
        .table tbody tr:hover { background-color: #f1f5f9 !important; transform: scale(1.001) !important; }
        
        /* DataTables wrapper spacing & fixes */
        .dataTables_wrapper { padding: 0.5rem 0 !important; }
        .dataTables_wrapper .row { align-items: center !important; margin-bottom: 0.75rem !important; }
        div.dataTables_wrapper div.dataTables_length label { font-weight: 500 !important; color: #64748b !important; font-size: 0.8rem !important; }
        div.dataTables_wrapper div.dataTables_length select { width: auto !important; display: inline-block !important; padding: 0.3rem 2rem 0.3rem 0.75rem !important; font-size: 0.8rem !important; border-radius: 6px !important; border: 1px solid #cbd5e1 !important; outline: none !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important; color: #475569 !important; }
        div.dataTables_wrapper div.dataTables_filter label { font-weight: 500 !important; color: #64748b !important; font-size: 0.8rem !important; }
        div.dataTables_wrapper div.dataTables_filter input { margin-left: 0.5em !important; display: inline-block !important; width: 200px !important; padding: 0.4rem 0.85rem !important; font-size: 0.8rem !important; border-radius: 20px !important; border: 1px solid #cbd5e1 !important; outline: none !important; transition: all 0.2s !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important; }
        div.dataTables_wrapper div.dataTables_filter input:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important; width: 230px !important; }
        
        /* Modern Pagination */
        .pagination { margin-bottom: 0 !important; justify-content: flex-end !important; display: flex !important; flex-wrap: wrap !important; list-style: none !important; gap: 4px !important; padding-left: 0 !important; }
        .page-item .page-link { font-size: 0.8rem !important; font-weight: 500 !important; padding: 0.3rem 0.75rem !important; border-radius: 6px !important; margin: 0 !important; border: none !important; color: #64748b !important; background: transparent !important; transition: all 0.2s !important; }
        .page-item .page-link:hover { background-color: #f1f5f9 !important; color: #334155 !important; }
        .page-item.active .page-link { background-color: #3b82f6 !important; color: #fff !important; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3) !important; }
        .page-item.disabled .page-link { color: #cbd5e1 !important; background: transparent !important; }

        /* Icon ONLY Buttons inside table - Small and Smooth */
        .table .btn { 
            padding: 0 !important; 
            font-size: 0 !important; /* Hide Text */
            border-radius: 6px !important; 
            box-shadow: none !important; 
            transition: all 0.2s !important; 
            display: inline-flex !important; 
            align-items: center !important; 
            justify-content: center !important; 
            margin-right: 4px !important;
            width: 28px !important;
            height: 28px !important;
        }
        .table .btn i, .table .btn svg { 
            width: 14px !important; 
            height: 14px !important; 
            font-size: 14px !important; 
            margin: 0 !important; 
        }
        .table .btn:hover { 
            transform: translateY(-1px) !important; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; 
            background-color: rgba(0,0,0,0.05); /* Slight darkening for better feedback */
        }
CSS;

if (strpos($content, '/* Kekinian Table & DataTables */') === false) {
    $content = str_replace('</head>', "<style>\n" . $override_css . "\n</style>\n</head>", $content);
    file_put_contents($file, $content);
    echo "Successfully injected override CSS.\n";
} else {
    // If it already exists, replace it
    $content = preg_replace('/<style>\s*\/\* Kekinian Table & DataTables \*\/.*?<\/style>/s', "<style>\n" . $override_css . "\n</style>", $content);
    file_put_contents($file, $content);
    echo "Successfully updated override CSS.\n";
}
?>
