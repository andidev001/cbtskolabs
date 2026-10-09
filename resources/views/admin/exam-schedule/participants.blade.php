@extends('layouts.admin')

@section('title', 'Mapping Peserta - ' . $schedule->name)

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.exam-event.index') }}">Event Ujian</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.exam-event.show', $schedule->examEvent->id) }}">{{ $schedule->examEvent->name }}</a></li>
            <li class="breadcrumb-item active">Peserta: {{ $schedule->name }}</li>
        </ol>
    </nav>
</div>

{{-- Info Header --}}
<div class="card shadow-sm border-0 mb-4" style="border-left: 4px solid #3b31c1 !important;">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="fw-bold mb-1">{{ $schedule->name }}</h5>
                <div class="d-flex gap-3 flex-wrap">
                    <span class="small text-muted"><i data-feather="calendar" class="me-1" style="width:13px;height:13px;"></i>{{ $schedule->examEvent->name }}</span>
                    <span class="small text-muted"><i data-feather="database" class="me-1" style="width:13px;height:13px;"></i>{{ $schedule->bankSoal->name }}</span>
                    <span class="small text-muted"><i data-feather="play-circle" class="me-1" style="width:13px;height:13px;"></i>{{ $schedule->start_at->format('d/m/Y H:i') }}</span>
                    <span class="small text-muted"><i data-feather="clock" class="me-1" style="width:13px;height:13px;"></i>{{ $schedule->duration }} menit</span>
                </div>
            </div>
            <div class="col-auto">
                <span class="badge px-3 py-2 {{ $schedule->is_active ? 'bg-success' : 'bg-danger' }}">
                    {{ $schedule->is_active ? 'Aktif' : 'Non-Aktif' }}
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: Pilih Group --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i data-feather="users" class="align-middle me-2" style="width:16px;height:16px;color:#3b31c1;"></i> Pilih Grup Peserta</h6>
                <small class="text-muted">Centang grup yang ikut ujian ini</small>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i data-feather="check-circle" style="width:14px;height:14px;" class="me-1"></i>{{ session('success') }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form action="{{ route('admin.exam-schedule.participants.sync', $schedule->id) }}" method="POST" id="groupForm">
                    @csrf
                    <div class="mb-3">
                        @forelse($allGroups as $group)
                        <label class="d-flex align-items-center border rounded p-2 mb-2 {{ in_array($group->id, $assignedGroupIds) ? 'border-primary bg-light' : '' }}"
                             id="group-card-{{ $group->id }}" style="cursor:pointer; width:100%;">
                            <input class="form-check-input mt-0 me-2 group-check" type="checkbox" name="group_ids[]"
                                   value="{{ $group->id }}" id="group{{ $group->id }}"
                                   {{ in_array($group->id, $assignedGroupIds) ? 'checked' : '' }}
                                   onchange="updateCard({{ $group->id }})">
                            <div class="w-100 d-flex justify-content-between align-items-center">
                                <span class="fw-bold mb-0">{{ $group->name }}</span>
                                <span class="badge bg-secondary rounded-pill">{{ $group->siswas_count }} siswa</span>
                            </div>
                        </label>
                        @empty
                        <p class="text-muted small text-center">Belum ada grup tersedia.</p>
                        @endforelse
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" style="background-color: #3b31c1; border: none;">
                            <i data-feather="save" class="align-middle me-1" style="width:15px;height:15px;"></i> Simpan Mapping
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: Daftar Peserta --}}
    <div class="col-md-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i data-feather="user-check" class="align-middle me-2" style="width:16px;height:16px;color:#3b31c1;"></i> Daftar Peserta</h6>
                <small class="text-muted">Siswa dari grup yang terpilih</small>
            </div>
            <div class="card-body p-3">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 w-100" id="participants-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="45" class="text-center ps-3">No</th>
                                <th>Nama Siswa</th>
                                <th>No. Ujian</th>
                                <th>Grup</th>
                                <th>Jurusan</th>
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
    $('#participants-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.exam-schedule.participants', $schedule->id) }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center ps-3 text-muted small'},
            {data: 'name', name: 'name', className: 'fw-bold'},
            {
                data: 'nomer_ujian', 
                name: 'nomer_ujian',
                render: function(data) {
                    return '<code class="bg-light px-2 py-1 rounded small">' + (data || '-') + '</code>';
                }
            },
            {
                data: 'group_name', 
                name: 'group.name',
                render: function(data) {
                    return '<span class="badge" style="background-color:#e8e6fd;color:#3b31c1;">' + data + '</span>';
                }
            },
            {data: 'jurusan_name', name: 'jurusan.name', className: 'small text-muted'}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
@endpush

<style>
    .breadcrumb-item a { color: #3b31c1; text-decoration: none; }
    ol.breadcrumb { list-style: none !important; display: flex !important; flex-wrap: wrap; padding-left: 0 !important; margin-bottom: 0 !important; gap: 0.5rem; }
    ol.breadcrumb li { display: flex; align-items: center; }
    .breadcrumb-item + .breadcrumb-item::before { content: '/'; margin-right: 0.5rem; color: #6c757d; }
    .table thead th { font-size: 0.8rem; font-weight: 600; color: #6c757d; text-transform: uppercase; }
    .table tbody td { font-size: 0.85rem; vertical-align: middle; }
    .form-check.border-primary { border-color: #3b31c1 !important; }
</style>
<script>
    function updateCard(groupId) {
        const cb = document.getElementById('group' + groupId);
        const card = document.getElementById('group-card-' + groupId);
        if (cb.checked) {
            card.classList.add('border-primary', 'bg-light');
        } else {
            card.classList.remove('border-primary', 'bg-light');
        }
    }
</script>
@endsection
