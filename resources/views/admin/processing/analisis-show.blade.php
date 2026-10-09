@extends(auth()->guard('admin')->check() ? 'layouts.admin' : 'layouts.guru')

@section('title', 'Detail Analisis: ' . $schedule->name)

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route(auth()->guard('admin')->check() ? 'admin.processing.analisis.index' : 'guru.processing.analisis.index') }}" class="btn btn-sm btn-secondary mb-3">
            <i data-feather="arrow-left" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Kembali
        </a>

        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Analisis Butir Soal: {{ $schedule->name }}</strong></h4>
                        <p class="text-muted small mb-0">{{ $schedule->examEvent->name }} | {{ $schedule->bankSoal->name }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <select id="filter-kelas" class="form-select form-select-sm" style="min-width: 140px;">
                            <option value="">Semua Kelas</option>
                            @foreach($groups as $g)
                                <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route(auth()->guard('admin')->check() ? 'admin.processing.analisis.export-excel' : 'guru.processing.analisis.export-excel', $schedule->id) }}" class="btn btn-success btn-sm d-inline-flex align-items-center text-nowrap py-1 px-2" style="font-size: 0.875rem;">
                            <i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i> Export Excel
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 w-100" id="analisis-detail-table">
                        <thead class="bg-light text-center">
                            <tr>
                                <th width="50">No</th>
                                <th>Butir Soal (Cuplikan)</th>
                                <th width="90">Tipe</th>
                                <th width="90">Level Kognitif</th>
                                <th width="90">Jawab Benar</th>
                                <th width="90">Total Peserta</th>
                                <th width="100">Indeks Kesukaran</th>
                                <th width="100">Tingkat Kesukaran</th>
                                <th width="100">Daya Pembeda</th>
                                <th width="100">Status DP</th>
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
    var table = $('#analisis-detail-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route(auth()->guard('admin')->check() ? 'admin.processing.analisis.show' : 'guru.processing.analisis.show', $schedule->id) }}",
            data: function (d) {
                d.group_id = $('#filter-kelas').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'cuplikan', name: 'question_text'},
            {
                data: 'type', 
                name: 'type', 
                className: 'text-center',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            {data: 'cognitive_level', name: 'cognitive_level', className: 'text-center fw-bold'},
            {data: 'correct_count', name: 'correct_count', orderable: false, searchable: false, className: 'text-center fw-bold text-success'},
            {data: 'total_participants', name: 'total_participants', orderable: false, searchable: false, className: 'text-center text-muted'},
            {data: 'difficulty_ratio', name: 'difficulty_ratio', orderable: false, searchable: false, className: 'text-center fw-bold'},
            {data: 'difficulty_level', name: 'difficulty_level', orderable: false, searchable: false, className: 'text-center'},
            {data: 'discrimination_index', name: 'discrimination_index', orderable: false, searchable: false, className: 'text-center fw-bold'},
            {data: 'discrimination_category', name: 'discrimination_category', orderable: false, searchable: false, className: 'text-center'}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });

    $('#filter-kelas').on('change', function() {
        table.draw();
        
        // Update export URL
        var exportBtn = $('a.btn-success:contains("Export Excel")');
        var baseUrl = "{{ route(auth()->guard('admin')->check() ? 'admin.processing.analisis.export-excel' : 'guru.processing.analisis.export-excel', $schedule->id) }}";
        var groupId = $(this).val();
        if (groupId) {
            exportBtn.attr('href', baseUrl + '?group_id=' + groupId);
        } else {
            exportBtn.attr('href', baseUrl);
        }
    });
});
</script>
@endpush

<style>
    .badge { font-weight: 500; padding: 4px 8px; }
    .table td { vertical-align: middle; }
</style>
@endsection


