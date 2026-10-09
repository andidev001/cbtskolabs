@extends(auth()->guard('admin')->check() ? 'layouts.admin' : 'layouts.guru')

@section('title', 'Leger Nilai Peserta')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body pt-4">
        <h4 class="mb-1"><strong>Leger Nilai Peserta</strong></h4>
        <p class="text-muted small mb-4">Laporan nilai kolektif per kelas/grup</p>

        <form action="{{ route(auth()->guard('admin')->check() ? 'admin.processing.leger.index' : 'guru.processing.leger.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label small fw-bold">Pilih Event</label>
                <select name="exam_event_id" class="form-select" required>
                    <option value="">Pilih Event...</option>
                    @foreach($examEvents as $event)
                        <option value="{{ $event->id }}" {{ request('exam_event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold">Pilih Grup/Kelas</label>
                <select name="group_id" class="form-select" required>
                    <option value="">Pilih Grup...</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i data-feather="search" class="align-middle me-1"></i> Tampilkan Leger
                </button>
            </div>
        </form>

        @if($selectedGroup && $selectedEvent)
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Leger: <strong>{{ $selectedGroup->name }}</strong> - Event: <strong>{{ $selectedEvent->name }}</strong></h5>
                <div>
                    <a href="{{ route(auth()->guard('admin')->check() ? 'admin.processing.leger.export-pdf' : 'guru.processing.leger.export-pdf', ['group_id' => request('group_id'), 'exam_event_id' => request('exam_event_id')]) }}" class="btn btn-danger btn-sm">
                        <i data-feather="file" class="align-middle me-1"></i> Cetak Leger (PDF)
                    </a>
                    <a href="{{ route(auth()->guard('admin')->check() ? 'admin.processing.leger.export-excel' : 'guru.processing.leger.export-excel', ['group_id' => request('group_id'), 'exam_event_id' => request('exam_event_id')]) }}" class="btn btn-success btn-sm ms-2">
                        <i data-feather="file-text" class="align-middle me-1"></i> Download Excel
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="bg-light text-center">
                        <tr>
                            <th width="50" rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Nama Siswa</th>
                            <th colspan="{{ $schedules->count() ?: 1 }}" class="align-middle">Mata Ujian / Skor</th>
                            <th rowspan="2" class="align-middle" width="80">Rata2</th>
                        </tr>
                        <tr>
                            @forelse($schedules as $sch)
                                <th class="small">{{ $sch->name }}</th>
                            @empty
                                <th class="text-muted small italic">Tidak ada jadwal</th>
                            @endforelse
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $s)
                            @php $totalScore = 0; $count = 0; @endphp
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $s->name }}</td>
                                @foreach($schedules as $sch)
                                    @php 
                                        $res = $results->get($s->id)?->where('exam_schedule_id', $sch->id)->first();
                                        if($res) { $totalScore += $res->score; $count++; }
                                    @endphp
                                    <td class="text-center {{ $res ? 'text-primary fw-bold' : 'text-muted' }}">
                                        {{ $res ? $res->score : '-' }}
                                    </td>
                                @endforeach
                                <td class="text-center bg-light fw-bold">
                                    {{ $count > 0 ? round($totalScore / $count, 1) : '0' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $schedules->count() + 3 }}" class="text-center py-4">Tidak ada data siswa di grup ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info">
                <i data-feather="info" class="align-middle me-2"></i> Silakan pilih filter di atas untuk menampilkan data leger.
            </div>
        @endif
    </div>
</div>
@endsection


