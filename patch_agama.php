<?php

// 1. Update ReligionController.php
$controllerFile = 'app/Http/Controllers/Admin/ReligionController.php';
$controllerContent = file_get_contents($controllerFile);
$controllerTarget = '<a href="\' . route(\'admin.religion.edit\', $row->id) . \'" class="btn btn-sm btn-primary me-1"><i class="align-middle" data-feather="edit-2"></i> Edit</a>';
$controllerReplacement = '<button type="button" class="btn btn-sm btn-primary me-1 btn-edit" data-id="\' . $row->id . \'" data-name="\' . htmlspecialchars($row->name) . \'"><i class="align-middle" data-feather="edit-2"></i></button>';

if (strpos($controllerContent, $controllerTarget) !== false) {
    $controllerContent = str_replace($controllerTarget, $controllerReplacement, $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
    echo "Controller patched.\n";
}

// 2. Update index.blade.php
$viewFile = 'resources/views/admin/religion/index.blade.php';
$viewContent = file_get_contents($viewFile);

$btnTarget = <<<HTML
    <a href="{{ route('admin.religion.create') }}" class="btn btn-primary">
        <i class="align-middle" data-feather="plus"></i> Tambah Agama
    </a>
HTML;

$btnReplacement = <<<HTML
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createReligionModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Agama
    </button>
HTML;

$viewContent = str_replace($btnTarget, $btnReplacement, $viewContent);

$modals = <<<HTML
<!-- Modal Tambah -->
<div class="modal fade" id="createReligionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Agama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.religion.store') }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Agama</label>
                        <input type="text" class="form-control" name="name" required placeholder="Misal: Islam">
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

<!-- Modal Edit -->
<div class="modal fade" id="editReligionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Agama</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="editReligionForm" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Agama</label>
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

if (strpos($viewContent, 'createReligionModal') === false) {
    $viewContent = str_replace('@push(\'scripts\')', $modals . "\n@push('scripts')", $viewContent);
}

$jsLogic = <<<JS
    // Modal Edit logic
    $('#religion-table').on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#edit_name').val(name);

        var actionUrl = "/admin/religion/" + id;
        $('#editReligionForm').attr('action', actionUrl);

        var editModal = new bootstrap.Modal(document.getElementById('editReligionModal'));
        editModal.show();
    });
JS;

if (strpos($viewContent, '.btn-edit') === false) {
    $viewContent = str_replace("});\n</script>", $jsLogic . "\n});\n</script>", $viewContent);
}

file_put_contents($viewFile, $viewContent);
echo "View patched.\n";

?>
