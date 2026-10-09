@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Data Mata Pelajaran</h1>
        <button type="button" class="btn btn-primary" data-bs-target="#createSubjectModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Mapel
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 w-100" id="subject-table">
                <thead class="bg-light">
                    <tr>
                        <th width="100">Kode</th>
                        <th>Nama Mata Pelajaran</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
@endpush
@endsection
