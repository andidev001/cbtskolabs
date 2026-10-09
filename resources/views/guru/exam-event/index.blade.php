@extends('layouts.guru')

@section('title', 'Manage event ujian')

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Manage event ujian</li>
        </ol>
    </nav>
</div>

<div class="mb-3">
    <button type="button" class="btn btn-primary" style="background-color: #3b31c1;" data-bs-toggle="modal" data-bs-target="#createModal">
        Tambah event ujian
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body pt-4">
        <h4 class="mb-1"><strong>Manage event ujian</strong></h4>
        <p class="text-muted small mb-4">Buat Edit Hapus event ujian</p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 w-100" id="event-table">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="fw-bold text-center">No</th>
                        <th class="fw-bold">Nama event</th>
                        <th class="fw-bold">Status</th>
                        <th width="250" class="fw-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Event -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createModalLabel">Tambah Event Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('guru.exam-event.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Event Ujian</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Ujian Tengah Semester Ganjil 2023" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Deskripsi (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Keterangan singkat tentang event ujian ini..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #3b31c1; border: none;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Event -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Edit Event Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Event Ujian</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Deskripsi (Opsional)</label>
                        <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label fw-bold" for="edit_is_active">Aktifkan Event</label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #3b31c1; border: none;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    document.body.appendChild(document.getElementById('createModal'));
    document.body.appendChild(document.getElementById('editModal'));

    $('#event-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('guru.exam-event.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'name', name: 'name', className: 'fw-bold'},
            {data: 'status_label', name: 'is_active', className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        let name = $(this).data('name');
        let description = $(this).data('description');
        let is_active = $(this).data('is_active');

        $('#editForm').attr('action', `/guru/exam-event/${id}`);
        $('#edit_name').val(name);
        $('#edit_description').val(description);
        
        if(is_active == 1) {
            $('#edit_is_active').prop('checked', true);
        } else {
            $('#edit_is_active').prop('checked', false);
        }

        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    });
});
</script>
@endpush

<style>
    .breadcrumb-item a { color: #3b31c1; text-decoration: none; }
    .table thead th { border-top: none; }
    .pagination .page-link { color: #3b31c1; cursor: pointer; }
    .pagination .page-item.active .page-link { color: #fff; }
    .btn-outline-success:hover { background-color: #28a745; color: #fff !important; }
</style>
@endsection
