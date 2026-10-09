@extends('layouts.admin')

@section('title', 'Monitoring: ' . $schedule->name)

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.monitoring.index') }}" class="btn btn-sm btn-secondary mb-3">
            <i data-feather="arrow-left" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Kembali
        </a>
        
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Monitoring Peserta: {{ $schedule->name }}</strong></h4>
                        <p class="text-muted small mb-0">Total Soal: <strong>{{ $totalQuestions }}</strong> | Token: <strong>{{ $schedule->token ?? '-' }}</strong></p>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="location.reload()">
                        <i data-feather="refresh-cw" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Refresh Status
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>No. Peserta</th>
                                <th>Nama Siswa</th>
                                <th>Grup/Kelas</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Progres</th>
                                <th class="text-center">Skor</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                            @php
                                $result = $results->get($student->id);
                                $answerCount = $answerCounts->get($student->id)->total ?? 0;
                                $progress = $totalQuestions > 0 ? round(($answerCount / $totalQuestions) * 100) : 0;
                                
                                $statusLabel = 'Belum Mulai';
                                $statusClass = 'bg-secondary';
                                
                                if($result) {
                                    if($result->status === 'blocked') {
                                        $statusLabel = 'Diblokir';
                                        $statusClass = 'bg-danger';
                                    } elseif($result->status === 'finished' || $result->finished_at) {
                                        $statusLabel = 'Selesai';
                                        $statusClass = 'bg-success';
                                    } else {
                                        $statusLabel = 'Sedang Mengerjakan';
                                        $statusClass = 'bg-primary';
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">{{ $student->nomer_ujian }}</td>
                                <td class="align-middle fw-bold">{{ $student->name }}</td>
                                <td class="align-middle">{{ $student->group->name ?? '-' }}</td>
                                <td class="text-center align-middle">
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                    @if($result && !$result->finished_at)
                                        <div class="small text-muted mt-1" style="font-size: 0.7rem;">
                                            Mulai: {{ $result->started_at->format('H:i') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 6px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <span class="small fw-bold">{{ $answerCount }}/{{ $totalQuestions }}</span>
                                    </div>
                                </td>
                                <td class="text-center align-middle fw-bold {{ $result && $result->finished_at ? 'text-success' : 'text-muted' }}">
                                    {{ $result && $result->finished_at ? $result->score : '-' }}
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex gap-1 justify-content-center">
                                        @if($result && !$result->finished_at && $result->status !== 'blocked')
                                        <form action="{{ route('admin.monitoring.force-finish', [$schedule->id, $student->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning text-white py-0 px-2" title="Paksa Selesai">
                                                <i data-feather="terminal" style="width: 12px; height: 12px;"></i> Selesai
                                            </button>
                                        </form>
                                        @endif

                                        @if($result && $result->status === 'blocked')
                                        <form action="{{ route('admin.monitoring.unblock', [$schedule->id, $student->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success py-0 px-2" onclick="return confirm('Buka blokir ujian untuk siswa ini agar bisa melanjutkan?')" title="Buka Blokir">
                                                <i data-feather="unlock" style="width: 12px; height: 12px;"></i> Buka Blokir
                                            </button>
                                        </form>
                                        @endif

                                        @if($result)
                                        <form action="{{ route('admin.monitoring.reset', [$schedule->id, $student->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger py-0 px-2" onclick="return confirm('Reset ujian untuk siswa ini? Semua jawaban akan dihapus.')" title="Reset Total Ujian">
                                                <i data-feather="rotate-ccw" style="width: 12px; height: 12px;"></i> Reset Total
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { border-top: none; font-size: 0.82rem; }
    .table tbody td { font-size: 0.82rem; }
    .badge { font-weight: 500; font-size: 0.70rem; padding: 3px 8px; }
    .progress { background-color: #e9ecef; border-radius: 10px; }
</style>
@endsection
