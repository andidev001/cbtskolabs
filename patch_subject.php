<?php

// 1. Update SubjectController.php
$controllerFile = 'app/Http/Controllers/Admin/SubjectController.php';
$controllerContent = file_get_contents($controllerFile);
$controllerTarget = '<a href="\' . route(\'admin.subject.edit\', $row->id) . \'" class="btn btn-sm btn-primary me-1"><i class="align-middle" data-feather="edit-2"></i> Edit</a>';
$controllerReplacement = '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" data-id="\' . $row->id . \'" data-code="\' . htmlspecialchars($row->code) . \'" data-name="\' . htmlspecialchars($row->name) . \'"><i class="align-middle" data-feather="edit-2"></i></button>';

if (strpos($controllerContent, $controllerTarget) !== false) {
    $controllerContent = str_replace($controllerTarget, $controllerReplacement, $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
    echo "Controller patched.\n";
} else {
    echo "Controller target not found, maybe already patched.\n";
}

// 2. Update index.blade.php
$viewFile = 'resources/views/admin/subject/index.blade.php';
$viewContent = file_get_contents($viewFile);

$btnReplacement = <<<HTML
    <button type="button" class="btn btn-primary" data-bs-target="#createSubjectModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Mapel
    </button>
HTML;

// Replace the Add button using regex to be safe against newlines
$viewContent = preg_replace('/<a href="\{\{\s*route\(\'admin\.subject\.create\'\)\s*\}\}".*?>.*?<\/a>/is', $btnReplacement, $viewContent);

$modals = <<<HTML
<!-- Modal Tambah Mapel -->
<div class="modal fade" id="createSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.subject.store') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Mata Pelajaran</label>
                        <input type="text" class="form-control" name="code" required placeholder="Misal: MTK">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Mata Pelajaran</label>
                        <input type="text" class="form-control" name="name" required placeholder="Misal: Matematika">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Mapel -->
<div class="modal fade" id="editSubjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Mata Pelajaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="editSubjectForm" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Mata Pelajaran</label>
                        <input type="text" class="form-control" name="code" id="edit_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Mata Pelajaran</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

HTML;

if (strpos($viewContent, 'createSubjectModal') === false) {
    $viewContent = str_replace('@push(\'scripts\')', $modals . "\n@push('scripts')", $viewContent);
}

$jsLogic = <<<JS

    // Modal Edit & Create logic
    var editModalEl = document.getElementById('editSubjectModal');
    if (editModalEl) {
        document.body.appendChild(editModalEl);
    }
    var createModalEl = document.getElementById('createSubjectModal');
    if (createModalEl) {
        document.body.appendChild(createModalEl);
    }

    $('#subject-table').on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var code = $(this).data('code');
        var name = $(this).data('name');

        $('#edit_code').val(code);
        $('#edit_name').val(name);

        var actionUrl = "/admin/subject/" + id;
        $('#editSubjectForm').attr('action', actionUrl);

        var editModal = new bootstrap.Modal(document.getElementById('editSubjectModal'));
        editModal.show();
    });

    var btnOpenCreate = document.querySelector('[data-bs-target="#createSubjectModal"]');
    if(btnOpenCreate) {
        btnOpenCreate.addEventListener('click', function(e) {
            e.preventDefault();
            var modal = new bootstrap.Modal(document.getElementById('createSubjectModal'));
            modal.show();
        });
        btnOpenCreate.removeAttribute('data-bs-toggle');
    }

</script>
JS;

if (strpos($viewContent, '.btn-edit') === false) {
    $viewContent = preg_replace('/<\/script>/', $jsLogic, $viewContent, 1);
}

file_put_contents($viewFile, $viewContent);
echo "View patched.\n";

?>
