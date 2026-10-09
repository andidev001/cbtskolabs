@extends('layouts.guru')

@section('title', 'Bank Soal Saya')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-2">
        <div class="row align-items-center">
            <div class="col-auto">
                <h5 class="card-title mb-0"><strong>Bank Soal Saya</strong></h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('guru.bank-soal.create') }}" class="btn btn-primary btn-sm">
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
        ajax: "{{ route('guru.bank-soal.index') }}",
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
                    let parts = [];
                    if (row.pg_count > 0) parts.push(row.pg_count + ' PG');
                    if (row.pgk_count > 0) parts.push(row.pgk_count + ' PGK');
                    if (row.ls_count > 0) parts.push(row.ls_count + ' Listening');
                    if (row.mj_count > 0) parts.push(row.mj_count + ' Menjodohkan');
                    if (row.is_count > 0) parts.push(row.is_count + ' Isian');
                    if (row.ur_count > 0) parts.push(row.ur_count + ' Esai');
                    if (row.mt_count > 0) parts.push(row.mt_count + ' Mengurutkan');
                    if (row.bs_count > 0) parts.push(row.bs_count + ' B/S');
                    if (row.st_count > 0) parts.push(row.st_count + ' S/TS');
                    
                    let setTampil = parts.length > 0 ? 'Set Tampil: ' + parts.join(', ') : 'Set Tampil: Belum diatur';

                    return '<div class="d-flex flex-column align-items-center gap-1">' +
                           '<span class="badge bg-primary" title="Total soal yang dibuat">' + data + ' Soal Dibuat</span>' +
                           '<span class="badge bg-secondary" style="font-size:0.65rem; white-space:normal; line-height:1.2; text-align:center;" title="Konfigurasi tampil di ujian">' + setTampil + '</span>' +
                           '</div>';
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
});
</script>
@endpush

<style>
    .card-body { padding: 0.75rem !important; }
    .table > :not(caption) > * > * { padding: 0.4rem 0.5rem !important; }
    .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.8rem; }
</style>
@endsection
