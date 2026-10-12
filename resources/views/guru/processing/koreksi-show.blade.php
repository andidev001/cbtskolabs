@extends('layouts.guru')

@section('title', 'Koreksi Esai')

@section('content')
<div class="d-flex align-items-center gap-2 mb-3">
    <a href="{{ route('guru.processing.koreksi.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-feather="arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0">Koreksi Esai</h1>
        <small class="text-muted">{{ optional($schedule->examEvent)->name }} &mdash; {{ optional($schedule->bankSoal)->name }}</small>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white fw-semibold">Daftar Peserta</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" id="koreksiTable" style="width:100%">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" width="50">No</th>
                        <th>Nama Siswa</th>
                        <th>Kelas/Grup</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($participants as $p)
                    @php $siswa = $p->siswa; @endphp
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="fw-semibold">{{ $siswa->name ?? '-' }}</td>
                        <td>{{ optional($siswa->group)->name ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-success">Selesai</span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('guru.processing.koreksi.siswa', [$schedule->id, $siswa->id]) }}" class="btn btn-sm btn-primary" title="Periksa">
                                <i data-feather="edit-3" class="align-middle" style="width: 14px; height: 14px;"></i>
                            </a>
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
    $('#koreksiTable').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            emptyTable: 'Belum ada peserta yang menyelesaikan ujian ini.'
        },
        drawCallback: function() { feather.replace(); }
    });
});
</script>
@endpush
@endsection
