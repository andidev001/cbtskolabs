<?php
$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

$target = <<<OLD
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
        .pagination { margin-bottom: 0; justify-content: flex-end; display: flex !important; flex-wrap: wrap; list-style: none !important; padding-left: 0; }
        .page-item .page-link { font-size: 0.8rem; padding: 0.3rem 0.75rem; border-radius: 4px; margin: 0 2px; border: 1px solid #eef2f7; color: #495057; }
        .page-item.active .page-link { background-color: var(--sidebar-bg, #0d3a85); border-color: var(--sidebar-bg, #0d3a85); color: #fff; }
OLD;

$replacement = <<<NEW
        /* Modern DataTables & Table Styling (Kekinian) */
        .table { font-size: 0.875rem; color: #475569; margin-bottom: 0 !important; border-collapse: separate; border-spacing: 0; }
        .table > :not(caption) > * > * { padding: 1rem 1.25rem; vertical-align: middle; border-bottom: 1px dashed #e2e8f0; }
        .table thead th { font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.8px; background-color: #f8fafc; color: #64748b; border-bottom: none !important; border-top: none; padding: 1.2rem 1.25rem; }
        .table thead tr th:first-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        .table thead tr th:last-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr { transition: all 0.2s ease; }
        .table tbody tr:hover { background-color: #f1f5f9; transform: scale(1.002); }
        
        /* Buttons inside table */
        .table .btn { padding: 0.35rem 0.75rem !important; font-size: 0.75rem !important; font-weight: 500; border-radius: 6px; box-shadow: none; transition: all 0.2s; }
        .table .btn i, .table .btn svg { width: 14px; height: 14px; margin-right: 2px; }
        .table .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        
        /* DataTables wrapper spacing & fixes */
        .dataTables_wrapper { padding: 0.5rem 0; }
        .dataTables_wrapper .row { align-items: center; margin-bottom: 1rem; }
        div.dataTables_wrapper div.dataTables_length label { font-weight: 500; color: #64748b; font-size: 0.85rem; }
        div.dataTables_wrapper div.dataTables_length select { width: auto; display: inline-block; padding: 0.4rem 2.5rem 0.4rem 1rem; font-size: 0.85rem; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); color: #475569; }
        div.dataTables_wrapper div.dataTables_filter label { font-weight: 500; color: #64748b; font-size: 0.85rem; }
        div.dataTables_wrapper div.dataTables_filter input { margin-left: 0.75em; display: inline-block; width: 250px; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 20px; border: 1px solid #cbd5e1; outline: none; transition: all 0.2s; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05); }
        div.dataTables_wrapper div.dataTables_filter input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); width: 280px; }
        div.dataTables_wrapper div.dataTables_info { padding-top: 1em; font-size: 0.85rem; color: #64748b; font-weight: 500; }
        div.dataTables_wrapper div.dataTables_paginate { margin-top: 1em; }
        
        /* Modern Pagination */
        .pagination { margin-bottom: 0; justify-content: flex-end; display: flex !important; flex-wrap: wrap; list-style: none !important; gap: 4px; padding-left: 0; }
        .page-item .page-link { font-size: 0.85rem; font-weight: 500; padding: 0.4rem 0.85rem; border-radius: 8px !important; margin: 0; border: none; color: #64748b; background: transparent; transition: all 0.2s; }
        .page-item .page-link:hover { background-color: #f1f5f9; color: #334155; }
        .page-item.active .page-link { background-color: #3b82f6; color: #fff; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3); }
        .page-item.disabled .page-link { color: #cbd5e1; background: transparent; }
NEW;

$content = str_replace($target, $replacement, $content);
file_put_contents($file, $content);
echo "Replaced successfully\n";
?>
