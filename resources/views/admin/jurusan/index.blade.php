@extends('layouts.admin')

@section('title', 'Data Jurusan')

@section('content')
<style>
    /* Strict Modal CSS fallback for Body placement */
    body > .modal { position: fixed !important; top: 0 !important; left: 0 !important; z-index: 9999 !important; width: 100vw !important; height: 100vh !important; background: rgba(0,0,0,0.5) !important; display: none; align-items: center; justify-content: center; }
    body > .modal.show { display: flex !important; }
    body > .modal .modal-dialog { position: relative !important; width: 100% !important; max-width: 500px !important; margin: auto !important; z-index: 10000 !important; }
    body > .modal .modal-content { background: #fff !important; border-radius: 8px !important; box-shadow: 0 10px 40px rgba(0,0,0,0.2) !important; border: none !important; overflow: hidden !important; }
    body > .modal .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid #f0f2f5; background-color: #f8f9fa; }
    body > .modal .modal-title { margin: 0; font-weight: 600; font-size: 1.05rem; color: #333; }
    body > .modal .btn-close { background: transparent; border: 0; font-size: 1.2rem; color: #666; cursor: pointer; padding: 0; margin: 0; }
    body > .modal .btn-close::before { content: '\2715'; }
    body > .modal .modal-body { padding: 1.5rem; max-height: calc(100vh - 150px); overflow-y: auto; }
    body > .modal .modal-footer { display: flex; justify-content: flex-end; align-items: center; gap: 0.5rem; padding: 1rem 1.5rem; border-top: 1px solid #f0f2f5; background-color: #f8f9fa; }
</style>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Data Jurusan</h1>
        <button type="button" class="btn btn-primary" data-bs-target="#createJurusanModal">
        <i class="align-middle" data-feather="plus"></i> Tambah Jurusan
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 w-100" id="jurusan-table">
                <thead class="bg-light">
                    <tr>
                        <th width="100">Kode</th>
                        <th>Nama Jurusan</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

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

<!-- Modal Tambah Jurusan -->
<div class="modal fade" id="createJurusanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jurusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.jurusan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Jurusan</label>
                        <input type="text" class="form-control" name="code" required placeholder="Misal: TKJ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Jurusan</label>
                        <input type="text" class="form-control" name="name" required placeholder="Misal: Teknik Komputer dan Jaringan">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Jurusan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#jurusan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.jurusan.index') }}",
        columns: [
            {
                data: 'code', 
                name: 'code',
                render: function(data) {
                    return '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            if(typeof feather !== 'undefined') feather.replace();
        }
    });

    var createModal = document.getElementById('createJurusanModal');
    if(createModal) {
        document.body.appendChild(createModal);
        
        var btnOpen = document.querySelector('[data-bs-target="#createJurusanModal"]');
        if(btnOpen) {
            btnOpen.addEventListener('click', function(e) {
                e.preventDefault();
                var modal = new bootstrap.Modal(createModal);
                modal.show();
            });
            btnOpen.removeAttribute('data-bs-toggle');
        }
    }
});

    // Modal Edit logic
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

</script>
@endpush
@endsection



