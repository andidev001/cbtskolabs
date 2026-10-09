<?php
$content = file_get_contents('resources/views/admin/subject/index.blade.php');

$new_header = <<<EOD
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Data Mata Pelajaran</h1>
    <div>
        <button type="button" class="btn btn-success shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#importSubjectModal">
            <i class="align-middle" data-feather="upload"></i> Import Excel
        </button>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createSubjectModal">
            <i class="align-middle" data-feather="plus"></i> Tambah Mapel
        </button>
    </div>
</div>
EOD;

$content = preg_replace('/<div class="d-flex justify-content-between align-items-center mb-3">.*?<\/div>/s', $new_header, $content, 1);
file_put_contents('resources/views/admin/subject/index.blade.php', $content);
echo "Done";

