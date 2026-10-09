<?php
$content = file_get_contents('resources/views/admin/subject/index.blade.php');

$old_header = <<<EOD
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Data Mata Pelajaran</h1>
        <button type="button" class="btn btn-primary" data-bs-target="#createSubjectModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Mapel
    </button>
</div>
EOD;

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

$content = str_replace($old_header, $new_header, $content);

$modal = <<<EOD
<!-- Modal Import Mapel -->
<div class="modal fade" id="importSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Import Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subject.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info border-0 shadow-sm p-3 mb-3">
                        <p class="mb-2"><strong>Petunjuk:</strong></p>
                        <ol class="mb-0 ps-3">
                            <li>Download template Excel melalui tombol di bawah.</li>
                            <li>Isi data tanpa mengubah baris pertama (header).</li>
                        </ol>
                    </div>
                    
                    <div class="mb-4 text-center">
                        <a href="{{ route('admin.subject.template') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                            <i data-feather="download" class="me-1"></i> Download Template Excel
                        </a>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih File Excel (.xlsx, .xls, .csv)</label>
                        <input type="file" name="file" class="form-control rounded-3" accept=".xlsx, .xls, .csv" required>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3 shadow-sm">
                        <i data-feather="upload" class="me-1"></i> Proses Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
EOD;

$content = str_replace("<!-- Modal Tambah Mapel -->", $modal . "\n\n<!-- Modal Tambah Mapel -->", $content);

// Append to body
$script_append = <<<EOD
    var importModalEl = document.getElementById('importSubjectModal');
    if (importModalEl) {
        document.body.appendChild(importModalEl);
    }
EOD;

$content = str_replace("document.body.appendChild(createModalEl);\n    }", "document.body.appendChild(createModalEl);\n    }\n" . $script_append, $content);

file_put_contents('resources/views/admin/subject/index.blade.php', $content);
echo "Done";

