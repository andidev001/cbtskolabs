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
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'PG']) }}">Pilihan Ganda</a>
                                <a class="dropdown-item" href="{{ route('admin.question.create', ['bankSoal' => $bankSoal->id, 'type' => 'Essay']) }}">Essay</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="{{ route('admin.bank-soal.import-word', $bankSoal->id) }}">Import dari Word (Docx)</a>
                                <a class="dropdown-item" href="#">Import dari Excel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">Daftar Pertanyaan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Pertanyaan</th>
                                <th width="100" class="text-center">Tipe</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bankSoal->questions as $index => $q)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        {!! Str::limit(strip_tags($q->question_text), 150) !!}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $q->type == 'PG' ? 'primary' : 'info' }}">{{ $q->type }}</span>
                                    </td>
                                    <td class="text-center">
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
                                    <td colspan="4" class="text-center py-5">
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
@endsection
