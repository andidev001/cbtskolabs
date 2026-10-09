@extends('layouts.admin')

@section('title', 'Data Peserta')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-2">
        <div class="row align-items-center">
            <div class="col-auto">
                <h5 class="card-title mb-0"><strong>Data Peserta</strong></h5>
            </div>
            <div class="col text-end">
                <div class="d-flex justify-content-end gap-1">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="align-middle" data-feather="file-plus"></i> Import
                    </button>
                    <a href="{{ route('admin.peserta.card-preview') }}" class="btn btn-info btn-sm text-white">
                        <i class="align-middle" data-feather="printer"></i> Cetak Kartu
                    </a>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createSiswaModal"><i class="align-middle" data-feather="plus"></i> Tambah</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover w-100 mb-0" id="peserta-table">
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th width="120">No Ujian</th>
                        <th>Nama Peserta</th>
                        <th width="200">Grup/Mapel</th>
                        <th width="100">Agama</th>
                        <th width="80">Sesi</th>
                        <th class="text-center" width="80">Status</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .card-body { padding: 0.75rem !important; }
    .table > :not(caption) > * > * { padding: 0.4rem 0.5rem !important; }
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 0.5rem; }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { margin-top: 0.5rem; }
    .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.8rem; }
    
    /* Fix for broken pagination list in some themes/DataTables combinations */
    .dataTables_paginate ul.pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        margin-top: 0;
        margin-bottom: 0;
    }
    
    /* Strict Modal CSS fallback for Body placement */
    body > .modal {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        z-index: 9999 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(0,0,0,0.5) !important;
        display: none;
        align-items: center;
        justify-content: center;
    }
    body > .modal.show {
        display: flex !important;
    }
    body > .modal .modal-dialog {
        position: relative !important;
        width: 100% !important;
        max-width: 800px !important;
        margin: auto !important;
        z-index: 10000 !important;
    }
    body > .modal .modal-content {
        background: #fff !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2) !important;
        border: none !important;
        overflow: hidden !important;
    }
    body > .modal .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f0f2f5;
        background-color: #f8f9fa;
    }
    body > .modal .modal-title {
        margin: 0;
        font-weight: 600;
        font-size: 1.05rem;
        color: #333;
    }
    body > .modal .btn-close {
        background: transparent;
        border: 0;
        font-size: 1.2rem;
        color: #666;
        cursor: pointer;
        padding: 0;
        margin: 0;
    }
    body > .modal .btn-close::before {
        content: '\2715';
    }
    body > .modal .modal-body {
        padding: 1.5rem;
        max-height: calc(100vh - 150px);
        overflow-y: auto;
    }
    body > .modal .modal-footer {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #f0f2f5;
        background-color: #f8f9fa;
    }
</style>

<!-- Scripts Section -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function() {
    var table = $('#peserta-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.peserta.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nomer_ujian', name: 'nomer_ujian'},
            {data: 'name', name: 'name'},
            {
                data: null, 
                name: 'group.name',
                render: function(data, type, row) {
                    return '<span class="badge bg-secondary">' + (row.group_name || '-') + '</span><br>' +
                           '<small class="text-muted">' + (row.jurusan_name || '-') + '</small>';
                }
            },
            {data: 'religion.name', name: 'religion.name', defaultContent: '-'},
            {
                data: 'sesi', 
                name: 'sesi', 
                render: function(data) {
                    return 'Sesi ' + (data || '-');
                }
            },
            {data: 'status_label', name: 'status', orderable: false, searchable: false, className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ entri",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            paginate: {
                previous: "Sebelumnya",
                next: "Selanjutnya"
            }
        },
        drawCallback: function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        }
    });
});
</script>

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
    var updateUrl = "{{ route('admin.peserta.update', ':id') }}";
    updateUrl = updateUrl.replace(":id", id);
    $("#editSiswaForm").attr("action", updateUrl);
    
    // Explicitly show modal using Bootstrap 5 API
    var editModalElement = document.getElementById('editSiswaModal');
    var editModal = bootstrap.Modal.getInstance(editModalElement);
    if (!editModal) {
        editModal = new bootstrap.Modal(editModalElement);
    }
    editModal.show();
});

// Pindahkan semua modal ke body untuk menghindari masalah position: fixed di dalam container yang ditransform
$(function() {
    var editModal = document.getElementById('editSiswaModal');
    if(editModal) document.body.appendChild(editModal);
    
    var createModal = document.getElementById('createSiswaModal');
    if(createModal) document.body.appendChild(createModal);
    
    var importModal = document.getElementById('importModal');
    if(importModal) document.body.appendChild(importModal);
});
</script>

<!-- Modal Import -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Peserta dari Excel/CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.peserta.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">File Excel/CSV</label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text">Pastikan format file sesuai dengan template.</div>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('admin.peserta.export-template') }}" class="btn btn-link btn-sm">
                            <i class="align-middle" data-feather="download"></i> Download Template Import
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">Mulai Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Peserta -->
<div class="modal fade" id="createSiswaModal" tabindex="-1" aria-labelledby="createSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createSiswaModalLabel">Tambah Data Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.peserta.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Ujian</label>
                            <input type="text" class="form-control" name="nomer_ujian" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username (untuk Login)</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Agama</label>
                            <select class="form-select" name="religion_id">
                                <option value="">-- Pilih Agama --</option>
                                @foreach($religions as $religion)
                                    <option value="{{ $religion->id }}">{{ $religion->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jurusan</label>
                            <select class="form-select" name="jurusan_id">
                                <option value="">-- Pilih Jurusan --</option>
                                @foreach($jurusans as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Grup / Kelas</label>
                            <select class="form-select" name="group_id">
                                <option value="">-- Pilih Grup --</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sesi</label>
                            <input type="text" class="form-control" name="sesi">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Peserta</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

@endsection




