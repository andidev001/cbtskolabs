@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Data Guru</h1>
    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createGuruModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Guru
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i data-feather="check-circle" class="me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 w-100" id="guru-table">
                <thead class="bg-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Nama / NIP</th>
                        <th>Username</th>
                        <th>Mata Pelajaran</th>
                        <th width="100" class="text-center">Status</th>
                        <th class="text-center" width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="createGuruModal" tabindex="-1" aria-labelledby="createGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="createGuruModalLabel">Tambah Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NIP (Opsional)</label>
                            <input type="text" name="nip" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">No HP (Opsional)</label>
                            <input type="text" name="phone" class="form-control rounded-3">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email (Opsional)</label>
                        <input type="email" name="email" class="form-control rounded-3">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control rounded-3" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto (Opsional)</label>
                        <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="is_active" class="form-select rounded-3">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 shadow-sm"><i data-feather="save" class="me-1" style="width: 16px; height: 16px;"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editGuruModal" tabindex="-1" aria-labelledby="editGuruModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="editGuruModalLabel">Edit Guru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editGuruForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="name" id="edit_name" class="form-control rounded-3" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">NIP (Opsional)</label>
                            <input type="text" name="nip" id="edit_nip" class="form-control rounded-3">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">No HP (Opsional)</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control rounded-3">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email (Opsional)</label>
                        <input type="email" name="email" id="edit_email" class="form-control rounded-3">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control rounded-3" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Password (Kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" class="form-control rounded-3">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto (Biarkan kosong jika tidak diubah)</label>
                        <input type="file" name="photo" class="form-control rounded-3" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="is_active" id="edit_is_active" class="form-select rounded-3">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 shadow-sm"><i data-feather="save" class="me-1" style="width: 16px; height: 16px;"></i> Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    // Append modals to body
    document.body.appendChild(document.getElementById('createGuruModal'));
    document.body.appendChild(document.getElementById('editGuruModal'));

    // Handle Edit Button Click
    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var nip = $(this).data('nip');
        var username = $(this).data('username');
        var email = $(this).data('email');
        var phone = $(this).data('phone');
        var isActive = $(this).data('is_active');

        $('#edit_name').val(name);
        $('#edit_nip').val(nip);
        $('#edit_username').val(username);
        $('#edit_email').val(email);
        $('#edit_phone').val(phone);
        $('#edit_is_active').val(isActive);

        $('#editGuruForm').attr('action', '/admin/guru/' + id);

        var editModal = new bootstrap.Modal(document.getElementById('editGuruModal'));
        editModal.show();
    });

    // Handle Delete using SweetAlert
    $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Guru?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'small-swal'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('#guru-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.guru.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name', render: function(data, type, row) {
                return '<div class="fw-semibold">' + data + '</div>' +
                       '<small class="text-muted">' + (row.nip || '-') + '</small>';
            }},
            {data: 'username', name: 'username'},
            {data: 'mapel', name: 'mapel', orderable: false},
            {data: 'status', name: 'status', orderable: false, searchable: false, className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'},
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });
});
</script>
@endpush
@endsection
