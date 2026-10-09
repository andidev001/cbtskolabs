@extends('layouts.admin')

@section('title', 'Detail Event: ' . $examEvent->name)

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.exam-event.index') }}">Manage event ujian</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $examEvent->name }}</li>
        </ol>
    </nav>
</div>

{{-- Header Card --}}
<div class="card shadow-sm border-0 mb-4" style="border-left: 4px solid #3b31c1 !important;">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                 style="width: 50px; height: 50px; background-color: #3b31c1; flex-shrink: 0;">
                <i data-feather="calendar" style="width: 24px; height: 24px;"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">{{ $examEvent->name }}</h5>
                <small class="text-muted">{{ $examEvent->description ?? 'Tidak ada keterangan' }}</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if($examEvent->is_active)
                <span class="badge bg-success px-3 py-2" style="font-size: 0.8rem;">Aktif</span>
            @else
                <span class="badge bg-secondary px-3 py-2" style="font-size: 0.8rem;">Non-Aktif</span>
            @endif
            <a href="{{ route('admin.exam-event.edit', $examEvent->id) }}" class="btn btn-sm btn-warning text-white ms-2" style="background-color: #f3b123; border: none;">
                <i class="align-middle" data-feather="edit-2" style="width: 14px; height: 14px;"></i> Edit Event
            </a>
            <a href="{{ route('admin.exam-event.index') }}" class="btn btn-sm btn-outline-secondary ms-1">
                <i class="align-middle" data-feather="arrow-left" style="width: 14px; height: 14px;"></i> Kembali
            </a>
        </div>
    </div>
</div>

{{-- Jadwal Ujian Section --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
        <div>
            <h6 class="card-title mb-0 fw-bold"><i class="align-middle me-2" data-feather="clock" style="width: 18px; height: 18px; color: #3b31c1;"></i> Jadwal Ujian</h6>
            <small class="text-muted">Daftar jadwal yang terdaftar dalam event ini</small>
        </div>
        <a href="{{ route('admin.exam-schedule.create') }}?event_id={{ $examEvent->id }}" class="btn btn-primary btn-sm" style="background-color: #3b31c1; border: none;">
            <i class="align-middle me-1" data-feather="plus" style="width: 14px; height: 14px;"></i> Tambah Jadwal
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center ps-3">No</th>
                        <th>Bank Soal</th>
                        <th>Nama Jadwal</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th class="text-center">Durasi</th>
                        <th class="text-center">Token</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                    <tr>
                        <td class="text-center align-middle ps-3">{{ $loop->iteration }}</td>
                        <td class="align-middle">
                            <span class="badge bg-info text-dark">{{ $schedule->bankSoal->code ?? '-' }}</span>
                            <small class="ms-1 text-muted">{{ $schedule->bankSoal->name }}</small>
                        </td>
                        <td class="align-middle fw-bold">{{ $schedule->name }}</td>
                        <td class="align-middle small">
                            <i class="align-middle me-1 text-muted" data-feather="play-circle" style="width: 12px; height: 12px;"></i>
                            {{ $schedule->start_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="align-middle small">
                            <i class="align-middle me-1 text-muted" data-feather="stop-circle" style="width: 12px; height: 12px;"></i>
                            {{ $schedule->end_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge bg-secondary">{{ $schedule->duration }} mnt</span>
                        </td>
                        <td class="text-center align-middle">
                            @if($schedule->token)
                                <code class="bg-light px-2 py-1 rounded" style="font-size: 0.75rem;">{{ $schedule->token }}</code>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if($schedule->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('admin.exam-schedule.participants', $schedule->id) }}" class="btn btn-sm px-2 d-inline-flex align-items-center text-white" style="background-color: #3b31c1; border: none;" title="Mapping Peserta">
                                    <i class="align-middle" data-feather="users" style="width: 13px; height: 13px;"></i>
                                </a>
                                <a href="{{ route('admin.exam-schedule.edit', $schedule->id) }}" class="btn btn-sm btn-warning text-white px-2" style="background-color: #f3b123; border: none;" title="Edit">
                                    <i class="align-middle" data-feather="edit-2" style="width: 13px; height: 13px;"></i>
                                </a>
                                <form action="{{ route('admin.exam-schedule.destroy', $schedule->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-2" onclick="return confirm('Hapus jadwal ini?')" style="background-color: #e35d5d; border: none;" title="Hapus">
                                        <i class="align-middle" data-feather="trash-2" style="width: 13px; height: 13px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i data-feather="inbox" style="width: 40px; height: 40px; opacity: 0.3; display: block; margin: 0 auto 10px;"></i>
                            Belum ada jadwal ujian untuk event ini.<br>
                            <a href="{{ route('admin.exam-schedule.create') }}" class="btn btn-sm btn-primary mt-3" style="background-color: #3b31c1; border: none;">
                                <i class="align-middle me-1" data-feather="plus" style="width: 14px; height: 14px;"></i> Tambah Jadwal Pertama
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(count($schedules) > 0)
    <div class="card-footer bg-white border-top">
        <small class="text-muted">{{ count($schedules) }} jadwal terdaftar dalam event ini.</small>
    </div>
    @endif
</div>

<style>
    .breadcrumb-item a { color: #3b31c1; text-decoration: none; }
    .table > :not(caption) > * > * { padding: 0.5rem 0.75rem; }
    .table thead th { font-size: 0.82rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.03em; }
    .table tbody td { font-size: 0.85rem; vertical-align: middle; }
</style>
@endsection
