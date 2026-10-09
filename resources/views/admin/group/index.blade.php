@extends('layouts.admin')

@section('title', 'Data Grup Peserta')

@section('content')
<style>
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
    <div>
        <h1 class="h4 fw-bold mb-0">Data Grup Peserta</h1>
        <p class="text-muted small mb-0">Kelola grup dan tambah anggota peserta ujian</p>
    </div>
    <button type="button" class="btn btn-primary" style="background-color:#3b31c1;border:none;" data-bs-toggle="modal" data-bs-target="#createGroupModal">
        <i class="align-middle me-1" data-feather="plus"></i> Tambah Grup
    </button>
</div>



<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover mb-0 w-100" id="group-table">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Grup</th>
                        <th class="text-center">Jumlah Anggota</th>
                        <th class="text-center" width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Grup -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Grup Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.group.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Grup / Kelas</label>
                        <input type="text" class="form-control" name="name" required placeholder="Misal: X TKJ 1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" style="background-color:#3b31c1;border:none;">Simpan Grup</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#group-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.group.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return '<div class="fw-bold"><i data-feather="users" class="me-2 text-muted" style="width:14px;height:14px;"></i>' + data + '</div>';
                }
            },
            {
                data: 'siswas_count', 
                name: 'siswas_count',
                className: 'text-center',
                render: function(data) {
                    return '<span class="badge rounded-pill bg-primary" style="background-color:#3b31c1!important;">' + data + ' peserta</span>';
                }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            if(typeof feather !== 'undefined') feather.replace();
        }
    });

    var createModal = document.getElementById('createGroupModal');
    if(createModal) {
        document.body.appendChild(createModal);
        var btnOpen = document.querySelector('[data-bs-target="#createGroupModal"]');
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
</script>
@endpush
@endsection


