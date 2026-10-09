<?php
$file = 'resources/views/admin/siswa/index.blade.php';
$content = file_get_contents($file);

$modalHtml = '
<!-- Modal Edit Peserta -->
<div class="modal fade" id="editSiswaModal" tabindex="-1" aria-labelledby="editSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSiswaModalLabel">Edit Data Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editSiswaForm" method="POST">
                @csrf
                @method("PUT")
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Ujian</label>
                            <input type="text" class="form-control" name="nomer_ujian" id="edit_nomer_ujian" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username (untuk Login)</label>
                            <input type="text" class="form-control" name="username" id="edit_username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                            <input type="password" class="form-control" name="password" id="edit_password">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Agama</label>
                            <select class="form-select" name="religion_id" id="edit_religion_id">
                                <option value="">-- Pilih Agama --</option>
                                @foreach($religions as $religion)
                                    <option value="{{ $religion->id }}">{{ $religion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jurusan</label>
                            <select class="form-select" name="jurusan_id" id="edit_jurusan_id">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Grup / Kelas</label>
                            <select class="form-select" name="group_id" id="edit_group_id">
                                <option value="">-- Pilih Grup --</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sesi Ujian</label>
                            <input type="text" class="form-control" name="sesi" id="edit_sesi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status">
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
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
';

$jsHtml = '
<script>
$(document).on("click", ".btn-edit", function() {
    var id = $(this).data("id");
    var nomer = $(this).data("nomer");
    var name = $(this).data("name");
    var username = $(this).data("username");
    var religion = $(this).data("religion");
    var jurusan = $(this).data("jurusan");
    var group = $(this).data("group");
    var sesi = $(this).data("sesi");
    var status = $(this).data("status");
    
    $("#edit_nomer_ujian").val(nomer);
    $("#edit_name").val(name);
    $("#edit_username").val(username);
    $("#edit_religion_id").val(religion);
    $("#edit_jurusan_id").val(jurusan);
    $("#edit_group_id").val(group);
    $("#edit_sesi").val(sesi);
    $("#edit_status").val(status);
    $("#edit_password").val("");
    
    // Set form action URL dynamically
    var updateUrl = "{{ route(\'admin.peserta.update\', \':id\') }}";
    updateUrl = updateUrl.replace(":id", id);
    $("#editSiswaForm").attr("action", updateUrl);
    
    $("#editSiswaModal").modal("show");
});
</script>
';

// Insert modal before @endsection
$content = str_replace('@endsection', $modalHtml . "\n@endsection", $content);
// Insert JS script before <!-- Modal Import -->
$content = str_replace('<!-- Modal Import -->', $jsHtml . "\n<!-- Modal Import -->", $content);

file_put_contents($file, $content);
