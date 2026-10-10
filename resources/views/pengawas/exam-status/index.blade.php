@extends('layouts.pengawas')

@section('title', 'Status Ujian & Rilis Token')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">
                        {!! session('success') !!}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Status Ujian & Rilis Token</strong></h4>
                        <p class="text-muted small mb-0">Kelola token ujian untuk siswa yang sedang berlangsung</p>
                    </div>
                    <button class="btn btn-primary" onclick="location.reload()" style="background-color: #3b31c1;">
                        <i data-feather="refresh-cw" class="align-middle me-1"></i> Refresh Data
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 w-100" id="token-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Ujian</th>
                                <th>Event</th>
                                <th class="text-center">Token Saat Ini</th>
                                <th width="250" class="text-center">Aksi Token</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#token-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('pengawas.exam-status.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {
                data: 'name', 
                name: 'name',
                render: function(data, type, row) {
                    return '<div class="fw-bold text-primary">' + data + '</div>' +
                           '<div class="small text-muted">' + (row.bank_soal_name || '-') + '</div>';
                }
            },
            {data: 'event_name', name: 'examEvent.name'},
            {data: 'token_label', name: 'token', orderable: false, searchable: false, className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });

    $('#token-table').on('click', '.btn-release-token', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        Swal.fire({
            title: 'Rilis Token Baru?',
            text: "Token lama akan diganti dengan yang baru.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Rilis!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('#token-table').on('click', '.btn-clear-token', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        Swal.fire({
            title: 'Hapus Token?',
            text: "Token akan dinonaktifkan dan siswa tidak bisa login dengan token ini.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { border-top: none; font-size: 0.85rem; }
    .table tbody td { font-size: 0.85rem; }
    .badge { font-weight: 500; font-size: 0.70rem; padding: 3px 8px; }
</style>
@endsection
