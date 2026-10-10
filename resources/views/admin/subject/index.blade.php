@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')

@section('content')
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

<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 w-100" id="subject-table">
                <thead class="bg-light">
                    <tr>
                        <th width="100">Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th>Khusus Agama</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

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
                    <div class="mb-3">
                        <label class="form-label">Khusus Agama (Opsional)</label>
                        <select class="form-select" name="religion_id">
                            <option value="">-- Umum (Semua Agama) --</option>
                            @foreach($religions ?? [] as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih jika mapel ini khusus agama tertentu.</small>
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
                    <div class="mb-3">
                        <label class="form-label">Khusus Agama (Opsional)</label>
                        <select class="form-select" name="religion_id" id="edit_religion_id">
                            <option value="">-- Umum (Semua Agama) --</option>
                            @foreach($religions ?? [] as $r)
                                <option value="{{ $r->id }}">{{ $r->name }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih jika mapel ini khusus agama tertentu.</small>
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

@push('scripts')
<script>
$(function() {
    $('#subject-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.subject.index') }}",
        columns: [
            {
                data: 'code', 
                name: 'code',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            {data: 'name', name: 'name'},
            {
                data: 'religion_name', 
                name: 'religion_name',
                render: function(data) {
                    if (data === 'Umum (Semua Agama)') {
                        return '<span class="text-muted small"><i data-feather="globe" style="width:12px;height:12px"></i> ' + data + '</span>';
                    }
                    return '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });
});

    // Modal Edit & Create logic
    var editModalEl = document.getElementById('editSubjectModal');
    if (editModalEl) {
        document.body.appendChild(editModalEl);
    }
    var createModalEl = document.getElementById('createSubjectModal');
    if (createModalEl) {
        document.body.appendChild(createModalEl);
    }
    var importModalEl = document.getElementById('importSubjectModal');
    if (importModalEl) {
        document.body.appendChild(importModalEl);
    }

    $('#subject-table').on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var code = $(this).data('code');
        var name = $(this).data('name');
        var religion = $(this).data('religion');

        $('#edit_code').val(code);
        $('#edit_name').val(name);
        $('#edit_religion_id').val(religion || '');

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
@endpush
@endsection
