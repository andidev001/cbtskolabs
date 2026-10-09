@extends('layouts.admin')

@section('title', 'Bank Soal')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-2">
        <div class="row align-items-center">
            <div class="col-auto">
                <h5 class="card-title mb-0"><strong>Bank Soal</strong></h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('admin.bank-soal.create') }}" class="btn btn-primary btn-sm">
                    <i class="align-middle" data-feather="plus"></i> Tambah Bank Soal
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover w-100 mb-0" id="bank-soal-table">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Kode</th>
                        <th>Mata Pelajaran</th>
                        <th>Nama Bank Soal</th>
                        <th class="text-center">Jumlah Soal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#bank-soal-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.bank-soal.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {
                data: 'code', 
                name: 'code',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            {data: 'subject_name', name: 'subject.name'},
            {
                data: 'name', 
                name: 'name',
                className: 'fw-bold'
            },
            {
                data: 'questions_count', 
                name: 'questions_count',
                className: 'text-center',
                render: function(data, type, row) {
                    return '<span class="badge bg-secondary">' + data + ' / ' + row.pg_count + '</span>';
                }
            },
            {data: 'status_label', name: 'status', orderable: false, searchable: false, className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });

    $(document).on('change', '.toggle-status', function() {
        var id = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var switchElement = $(this);
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        $.ajax({
            url: "{{ url('admin/bank-soal') }}/" + id + "/toggle-status",
            type: "PATCH",
            data: {
                _token: "{{ csrf_token() }}",
                status: isChecked ? 1 : 0
            },
            success: function(response) {
                Toast.fire({
                    icon: 'success',
                    title: response.message || 'Status diperbarui'
                });
            },
            error: function() {
                // Revert switch if failed
                switchElement.prop('checked', !isChecked);
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal memperbarui status'
                });
            }
        });
    });
});
</script>
@endpush

<style>
    .card-body { padding: 0.75rem !important; }
    .table > :not(caption) > * > * { padding: 0.4rem 0.5rem !important; }
    .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.8rem; }
</style>
@endsection
