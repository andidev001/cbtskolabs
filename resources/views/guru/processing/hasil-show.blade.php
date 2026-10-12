@extends('layouts.guru')

@section('title', 'Hasil Ujian — ' . ($schedule->bankSoal->name ?? ''))

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('guru.processing.hasil.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-feather="arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0">Hasil Ujian</h1>
        <small class="text-muted">
            {{ $schedule->examEvent->name ?? '-' }} &mdash; {{ $schedule->bankSoal->name ?? '-' }}
        </small>
    </div>
</div>

{{-- Ringkasan --}}
@php
    $total     = $results->count();
    $avgScore  = $total ? round($results->avg('score'), 1) : 0;
    $highest   = $total ? $results->max('score') : 0;
    $lowest    = $total ? $results->min('score') : 0;
    
    // Cek tipe soal
    $qTypes = \App\Models\Question::where('bank_soal_id', $schedule->bank_soal_id)->pluck('type')->unique()->toArray();
    $isOnlyPG = (count($qTypes) === 1 && in_array('PG', $qTypes));
    $hasEssay = (in_array('Essay', $qTypes) || in_array('UR', $qTypes));
    
    // Daftar Kelas Unik
    $groups = $results->mapWithKeys(function($r) {
        $g = $r->siswa->group;
        return $g ? [$g->id => $g->name] : [];
    })->filter()->sort();
@endphp

@if(!$isOnlyPG)
<div class="alert alert-info d-flex align-items-center mb-4">
    <i data-feather="info" class="me-2"></i>
    <div>
        <strong>Catatan:</strong> Ujian ini mengandung tipe soal selain Pilihan Ganda (PG). 
        Kolom <em>Benar/Salah</em> disembunyikan karena ada penilaian parsial/proporsional. 
        @if($hasEssay)
        <br>Terdapat soal <strong>Esai</strong>. Pastikan Anda sudah memberikan nilai esai di menu <a href="{{ route('guru.processing.koreksi.index') }}" class="alert-link">Koreksi Esai</a> agar Nilai Akhir maksimal.
        @endif
    </div>
</div>
@endif

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-3 text-primary" id="stat-total">{{ $total }}</div>
            <div class="text-muted small">Peserta</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-3 text-success" id="stat-avg">{{ $avgScore }}</div>
            <div class="text-muted small">Rata-rata</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-3 text-info" id="stat-high">{{ $highest }}</div>
            <div class="text-muted small">Tertinggi</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center p-3">
            <div class="fw-bold fs-3 text-danger" id="stat-low">{{ $lowest }}</div>
            <div class="text-muted small">Terendah</div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span>Daftar Nilai Siswa</span>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <form action="{{ route('guru.processing.hasil.recalculate', $schedule->id) }}" method="POST" id="form-recalculate" class="d-inline">
                @csrf
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-recalculate" onclick="if(confirm('Kalkulasi ulang nilai semua siswa yang telah selesai ujian ini?')) this.form.submit();">
                    <i data-feather="refresh-cw" style="width: 14px; height: 14px;"></i> Hitung Ulang Nilai
                </button>
            </form>
            <a href="{{ route('guru.processing.hasil.export-excel', $schedule->id) }}" id="btn-export-excel" class="btn btn-sm btn-outline-success">
                <i data-feather="file-text" style="width: 14px; height: 14px;"></i> Export Excel
            </a>
            <select id="filter-kelas" class="form-select form-select-sm" style="width: auto; min-width: 130px;">
                <option value="" data-id="">Semua Kelas</option>
                @foreach($groups as $id => $name)
                    <option value="{{ $name }}" data-id="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
            <span class="badge bg-primary" id="badge-total">{{ $total }} peserta</span>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" id="tableHasil" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>No. Peserta</th>
                        <th>Nama Siswa</th>
                        <th>Kelas/Grup</th>
                        @if($isOnlyPG)
                        <th class="text-center">Benar</th>
                        <th class="text-center">Salah</th>
                        @endif
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $result)
                    @php $siswa = $result->siswa; @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $siswa->nomer_ujian ?? '-' }}</td>
                        <td class="fw-semibold">{{ $siswa->name ?? '-' }}</td>
                        <td>{{ optional($siswa->group)->name ?? '-' }}</td>
                        
                        @if($isOnlyPG)
                        <td class="text-center text-success fw-bold">{{ $result->correct_answers }}</td>
                        <td class="text-center text-danger fw-bold">
                            {{ ($result->total_questions ?? 0) - $result->correct_answers }}
                        </td>
                        @endif

                        <td class="text-center">
                            @php $score = $result->score; @endphp
                            <span class="badge fs-6 bg-{{ $score >= 75 ? 'success' : ($score >= 60 ? 'warning text-dark' : 'danger') }}">
                                {{ $score }}
                            </span>
                        </td>
                        <td class="text-center small text-muted">
                            {{ $result->finished_at ? $result->finished_at->format('d/m/Y H:i') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    var table = $('#tableHasil').DataTable({
        @if($results->count() > 0)
        order: [[{{ $isOnlyPG ? 6 : 4 }}, 'desc']],
        @else
        order: [],
        @endif
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: 'Belum ada hasil ujian masuk.'
        },
        drawCallback: function() { feather.replace(); }
    });

    var baseExportUrl = "{{ route('guru.processing.hasil.export-excel', $schedule->id) }}";

    $('#filter-kelas').on('change', function() {
        var val = $(this).val();
        var id = $(this).find('option:selected').data('id');
        var escapedVal = $.fn.dataTable.util.escapeRegex(val);
        
        // Column index 3 adalah Kelas/Grup
        table.column(3).search(escapedVal ? '^' + escapedVal + '$' : '', true, false).draw();

        // Update link export
        var exportUrl = baseExportUrl;
        if (id) {
            exportUrl += "?group_id=" + id;
        }
        $('#btn-export-excel').attr('href', exportUrl);
    });
});
</script>
@endpush
@endsection
