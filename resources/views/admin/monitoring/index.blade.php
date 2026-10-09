@extends('layouts.admin')

@section('title', 'Status Peserta (Monitoring)')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Status Peserta</strong></h4>
                        <p class="text-muted small mb-0">Pilih ujian untuk memantau aktivitas peserta secara real-time</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 w-100" id="monitoring-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Ujian</th>
                                <th>Event</th>
                                <th class="text-center">Bank Soal</th>
                                <th width="150" class="text-center">Aksi</th>
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
    $('#monitoring-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.monitoring.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'name', name: 'name', className: 'fw-bold text-primary'},
            {data: 'event_name', name: 'examEvent.name'},
            {data: 'bank_soal_name', name: 'bankSoal.name', className: 'text-center small text-muted'},
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
    .table thead th { border-top: none; font-size: 0.85rem; }
    .table tbody td { font-size: 0.85rem; }
</style>
@endsection
