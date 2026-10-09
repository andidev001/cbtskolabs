@extends('layouts.admin')

@section('title', 'Hasil Ujian Peserta')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body pt-4">
        <h4 class="mb-1"><strong>Hasil Ujian Peserta</strong></h4>
        <p class="text-muted small mb-4">Daftar hasil ujian yang telah selesai dilaksanakan</p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 w-100" id="hasil-table">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Ujian</th>
                        <th>Event</th>
                        <th>Bank Soal</th>
                        <th width="150" class="text-center">Aksi</th>
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
    $('#hasil-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.processing.hasil.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'name', name: 'name', className: 'fw-bold text-primary'},
            {data: 'event_name', name: 'examEvent.name'},
            {data: 'bank_soal_name', name: 'bankSoal.name'},
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
@endsection
