@extends('layouts.admin')

@section('title', 'Mapping Mata Pelajaran — ' . $guru->name)

@section('content')
<div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-feather="arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 mb-0">Mapping Mata Pelajaran</h1>
        <small class="text-muted">Guru: <strong>{{ $guru->name }}</strong>{{ $guru->nip ? ' — NIP: ' . $guru->nip : '' }}</small>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
    <i data-feather="check-circle" class="me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="row g-4">
    {{-- Form mapping --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header text-white d-flex align-items-center gap-2" style="background-color: #4361ee;">
                <i data-feather="book-open"></i>
                <span class="fw-semibold">Pilih Mata Pelajaran yang Diampu</span>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.guru.subjects.sync', $guru->id) }}" method="POST">
                    @csrf

                    @if($subjects->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i data-feather="alert-circle" style="width:40px;height:40px;opacity:.3"></i>
                            <p class="mt-2">Belum ada mata pelajaran. <a href="{{ route('admin.subject.create') }}">Tambahkan dulu.</a></p>
                        </div>
                    @else
                        <div class="mb-3">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="checkAll()">Pilih Semua</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="uncheckAll()">Batal Semua</button>
                                </div>
                                <div class="flex-grow-1" style="max-width: 300px;">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white"><i data-feather="search" style="width: 14px; height: 14px;"></i></span>
                                        <input type="text" id="search-subject" class="form-control" placeholder="Cari mata pelajaran...">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border rounded-3 shadow-sm" style="max-height: 350px; overflow-y: auto;">
                                <div class="list-group list-group-flush" id="subject-list">
                                    @foreach($subjects as $subject)
                                    <label class="list-group-item list-group-item-action d-flex align-items-center subject-item {{ $guru->subjects->contains($subject->id) ? 'list-group-item-primary' : '' }}" 
                                           style="cursor: pointer;" 
                                           id="item-{{ $subject->id }}" 
                                           data-name="{{ strtolower($subject->name . ' ' . $subject->code) }}">
                                        <div class="form-check m-0 fs-5">
                                            <input class="form-check-input subject-cb" type="checkbox"
                                                   name="subject_ids[]"
                                                   value="{{ $subject->id }}"
                                                   {{ $guru->subjects->contains($subject->id) ? 'checked' : '' }}
                                                   onchange="toggleHighlight({{ $subject->id }}, this.checked)">
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-semibold text-dark mb-0">{{ $subject->name }}</div>
                                            @if($subject->code)
                                            <small class="text-muted">{{ $subject->code }}</small>
                                            @endif
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i data-feather="save" class="me-1"></i> Simpan Mapping
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>

    {{-- Info guru --}}
    <div class="col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light fw-semibold">
                <i data-feather="user" class="me-1"></i> Profil Guru
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    @if($guru->photo)
                        <img src="{{ asset('storage/' . $guru->photo) }}" alt="Foto"
                             class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                    @else
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:64px;height:64px;font-size:1.5rem; background-color: #4361ee;">
                            {{ strtoupper(substr($guru->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <div class="fw-bold fs-5">{{ $guru->name }}</div>
                        <div class="text-muted small">{{ $guru->nip ?: 'NIP belum diisi' }}</div>
                        <span class="badge {{ $guru->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $guru->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                </div>

                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="100">Username</td>
                        <td><strong>{{ $guru->username }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td class="text-break">{{ $guru->email ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">No. HP</td>
                        <td>{{ $guru->phone ?: '-' }}</td>
                    </tr>
                </table>

                <hr>
                <div class="fw-semibold mb-2">Mapel yang diampu saat ini:</div>
                @forelse($guru->subjects as $s)
                    <span class="badge me-1 mb-1 py-2 px-3" style="background-color: #4361ee; color: #fff;">
                        <i data-feather="book" style="width:12px;height:12px"></i>
                        {{ $s->name }} @if($s->code) ({{ $s->code }}) @endif
                    </span>
                @empty
                    <span class="text-muted fst-italic">Belum ada mata pelajaran</span>
                @endforelse
            </div>
        </div>


    </div>
</div>

@push('scripts')
<script>
function toggleHighlight(id, checked) {
    const item = document.getElementById('item-' + id);
    if (checked) {
        item.classList.add('list-group-item-primary');
    } else {
        item.classList.remove('list-group-item-primary');
    }
}

function checkAll() {
    document.querySelectorAll('.subject-cb').forEach(cb => {
        cb.checked = true;
        toggleHighlight(cb.value, true);
    });
}

function uncheckAll() {
    document.querySelectorAll('.subject-cb').forEach(cb => {
        cb.checked = false;
        toggleHighlight(cb.value, false);
    });
}

// Search Filter
document.getElementById('search-subject')?.addEventListener('input', function() {
    const keyword = this.value.toLowerCase();
    document.querySelectorAll('.subject-item').forEach(item => {
        const name = item.getAttribute('data-name');
        if(name.includes(keyword)) {
            item.classList.remove('d-none');
            item.classList.add('d-flex');
        } else {
            item.classList.remove('d-flex');
            item.classList.add('d-none');
        }
    });
});
</script>
@endpush
@endsection
