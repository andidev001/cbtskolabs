@extends('layouts.admin')

@section('title', 'Koreksi: ' . $schedule->name)

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.processing.koreksi.index') }}" class="btn btn-sm btn-secondary mb-3">
            <i data-feather="arrow-left" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Kembali
        </a>
        
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Koreksi Esai: {{ $schedule->name }}</strong></h4>
                        <p class="text-muted small mb-0">Klik nama peserta untuk mulai melakukan pemeriksaan jawaban esai.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>No. Peserta</th>
                                <th>Nama Siswa</th>
                                <th>Grup/Kelas</th>
                                <th class="text-center">Status Koreksi</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($participants as $participant)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $participant->siswa->nomer_ujian }}</td>
                                <td class="align-middle fw-bold text-primary">{{ $participant->siswa->name }}</td>
                                <td class="align-middle">{{ $participant->siswa->group->name ?? '-' }}</td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-warning">Belum Diperiksa</span>
                                </td>
                                <td class="text-center align-middle">
                                    <a href="{{ route('admin.processing.koreksi.siswa', [$schedule->id, $participant->siswa_id]) }}" class="btn btn-sm btn-primary">
                                        <i data-feather="edit-3" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Periksa
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted"> Belum ada peserta yang mengumpulkan jawaban.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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
