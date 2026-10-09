@extends('layouts.guru')
@section('title', 'Koreksi Esai')
@section('content')
<h1 class="h3 mb-3">Koreksi Esai</h1>
<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <table class="table table-hover w-100" id="koreksi-table">
            <thead class="bg-light">
                <tr>
                    <th>#</th><th>Event Ujian</th><th>Bank Soal</th><th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@push('scripts')
<script>
$(function() {
    $('#koreksi-table').DataTable({
        processing: true, serverSide: true,
        ajax: "{{ route('guru.processing.koreksi.index') }}",
        columns: [
            {data:'DT_RowIndex', orderable:false, searchable:false},
            {data:'event_name'}, {data:'bank_soal_name'},
            {data:'action', orderable:false, searchable:false, className:'text-center'},
        ],
        language: {url:'//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'},
        drawCallback: function(){ feather.replace(); }
    });
});
</script>
@endpush
@endsection
