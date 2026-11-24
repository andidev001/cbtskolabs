@extends('layouts.admin')

@section('title', 'Kelola Soal - ' . $bankSoal->name)

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-secondary btn-sm">
            <i class="align-middle" data-feather="arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="mb-1"><strong>{{ $bankSoal->name }}</strong></h4>
                        <p class="text-muted mb-0">
                            Subject: {{ $bankSoal->subject->name }} | Code: {{ $bankSoal->code }}
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group">
                            <button class="btn btn-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="align-middle" data-feather="plus"></i> Tambah Soal
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'pg']) }}">Pilihan Ganda</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'pgk']) }}">PG Kompleks</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'ls']) }}">Listening</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'mj']) }}">Menjodohkan</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'is']) }}">Isian Singkat</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'ur']) }}">Uraian / Essay</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'mt']) }}">Mengurutkan</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'bs']) }}">Benar / Salah</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'st']) }}">Setuju / Tidak</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('admin.bank-soal.import-word', $bankSoal->id) }}">Import dari Word (Docx)</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="bulk-delete-form" action="{{ route('admin.question.bulk-destroy', $bankSoal->id) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
            <div id="bulk-delete-inputs"></div>
        </form>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-2">
                <h5 class="card-title mb-0">Daftar Pertanyaan ({{ $bankSoal->questions->count() }} Soal)</h5>
                <button type="button" id="btn-bulk-delete" class="btn btn-danger btn-sm d-none">
                    <i class="align-middle me-1" data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                    Hapus Terpilih (<span id="selected-count">0</span>)
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="40" class="text-center">
                                    <input type="checkbox" class="form-check-input" id="check-all-questions" title="Pilih Semua">
                                </th>
                                <th width="50" class="text-center">No</th>
                                <th>Pertanyaan</th>
                                <th width="100" class="text-center">Tipe</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bankSoal->questions as $index => $q)
                                <tr>
                                    <td class="text-center align-middle">
                                        <input type="checkbox" class="form-check-input question-checkbox" value="{{ $q->id }}">
                                    </td>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div>{!! Str::limit(strip_tags($q->question_text), 150) !!}</div>
                                        @if(preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $q->question_text, $imgMatches))
                                            <div class="d-flex flex-wrap gap-2 mt-1">
                                                @foreach($imgMatches[1] as $imgSrc)
                                                    <img src="{{ $imgSrc }}" alt="Gambar Soal" class="img-thumbnail" style="max-height: 60px; width: auto;">
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-{{ $q->type == 'PG' ? 'primary' : 'info' }}">{{ $q->type }}</span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.question.show', $q->id) }}" class="btn btn-primary text-white" title="Lihat Soal">
                                                <i data-feather="eye" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <a href="{{ route('admin.question.edit', $q->id) }}" class="btn btn-info text-white" title="Edit Soal">
                                                <i data-feather="edit-2" style="width: 14px; height: 14px;"></i>
                                            </a>
                                            <form action="{{ route('admin.question.destroy', $q->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Hapus soal ini?')" title="Hapus Soal">
                                                    <i data-feather="trash-2" style="width: 14px; height: 14px;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="mb-2" data-feather="help-circle" style="width: 48px; height: 48px;"></i>
                                            <p>Belum ada soal di Bank Soal ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js" onload="renderMathInElement(document.body, {delimiters: [{left: '$$', right: '$$', display: true}, {left: '\\[', right: '\\]', display: true}, {left: '\\(', right: '\\)', display: false}], throwOnError: false});"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check-all-questions');
    const checkboxes = document.querySelectorAll('.question-checkbox');
    const btnBulkDelete = document.getElementById('btn-bulk-delete');
    const selectedCountEl = document.getElementById('selected-count');
    const bulkForm = document.getElementById('bulk-delete-form');
    const bulkInputs = document.getElementById('bulk-delete-inputs');

    function updateSelectionState() {
        const checked = document.querySelectorAll('.question-checkbox:checked');
        const count = checked.length;
        selectedCountEl.textContent = count;

        if (count > 0) {
            btnBulkDelete.classList.remove('d-none');
        } else {
            btnBulkDelete.classList.add('d-none');
        }

        if (checkAll && checkboxes.length > 0) {
            checkAll.checked = (count === checkboxes.length);
            checkAll.indeterminate = (count > 0 && count < checkboxes.length);
        }
    }

    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
            });
            updateSelectionState();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectionState);
    });

    if (btnBulkDelete) {
        btnBulkDelete.addEventListener('click', function() {
            const checked = document.querySelectorAll('.question-checkbox:checked');
            if (checked.length === 0) return;

            const submitBulk = function() {
                bulkInputs.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'question_ids[]';
                    input.value = cb.value;
                    bulkInputs.appendChild(input);
                });
                bulkForm.submit();
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Hapus ' + checked.length + ' Soal Terpilih?',
                    text: 'Soal yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitBulk();
                    }
                });
            } else if (confirm('Yakin ingin menghapus ' + checked.length + ' soal terpilih?')) {
                submitBulk();
            }
        });
    }
});
</script>
@endpush
@endsection
