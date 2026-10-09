@extends('layouts.admin')

@section('title', 'Data Agama')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Data Agama</h1>
        <button type="button" class="btn btn-primary" data-bs-target="#createReligionModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Agama
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 w-100" id="religion-table">
                <thead class="bg-light">
                    <tr>
                        <th width="80">No</th>
                        <th>Nama Agama</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

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

@push('scripts')
<script>
$(function() {
    $('#religion-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.religion.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
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
    var editModalEl = document.getElementById('editReligionModal');
    if (editModalEl) {
        document.body.appendChild(editModalEl);
    }
    var createModalEl = document.getElementById('createReligionModal');
    if (createModalEl) {
        document.body.appendChild(createModalEl);
    }

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


    // Modal Create manual logic
    var btnOpenCreate = document.querySelector('[data-bs-target="#createReligionModal"]');
    if(btnOpenCreate) {
        btnOpenCreate.addEventListener('click', function(e) {
            e.preventDefault();
            var modal = new bootstrap.Modal(document.getElementById('createReligionModal'));
            modal.show();
        });
        btnOpenCreate.removeAttribute('data-bs-toggle');
    }

</script>
@endpush
@endsection
