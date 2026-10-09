<?php
$file = 'resources/views/admin/jurusan/index.blade.php';
$content = file_get_contents($file);

// Add the modal HTML after createJurusanModal
$modalHtml = <<<HTML
<!-- Modal Edit Jurusan -->
<div class="modal fade" id="editJurusanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" id="editJurusanForm" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="PUT">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan</label>
                        <input type="text" class="form-control" name="code" id="edit_code" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
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

if (strpos($content, 'editJurusanModal') === false) {
    $content = str_replace('<!-- Modal Tambah Jurusan -->', $modalHtml . "\n\n<!-- Modal Tambah Jurusan -->", $content);
}

// Add the JS logic before });
$jsLogic = <<<JS
    var editModalEl = document.getElementById('editJurusanModal');
    if (editModalEl) {
        document.body.appendChild(editModalEl);
        var editModal = new bootstrap.Modal(editModalEl);
    }

    $('#jurusan-table').on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var code = $(this).data('code');
        var name = $(this).data('name');

        $('#edit_code').val(code);
        $('#edit_name').val(name);

        var actionUrl = "/admin/jurusan/" + id;
        $('#editJurusanForm').attr('action', actionUrl);

        if(editModal) {
            editModal.show();
        }
    });
JS;

if (strpos($content, '.btn-edit') === false) {
    $content = str_replace("});\n</script>", $jsLogic . "\n});\n</script>", $content);
}

file_put_contents($file, $content);
echo "Jurusan index patched successfully.\n";
?>
