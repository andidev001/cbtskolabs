@extends('layouts.admin')

@section('title', 'Tambah Jadwal Ujian')

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.exam-schedule.index') }}">Manage Jadwal ujian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <i class="align-middle me-2 text-primary" data-feather="plus-circle"></i>
                <h6 class="card-title mb-0 fw-bold">Tambah Jadwal Ujian</h6>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('admin.exam-schedule.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Event Ujian</label>
                            <select name="exam_event_id" class="form-select @error('exam_event_id') is-invalid @enderror" required>
                                <option value="">Pilih Event...</option>
                                @foreach($examEvents as $event)
                                    <option value="{{ $event->id }}" {{ (old('exam_event_id') ?? request('event_id')) == $event->id ? 'selected' : '' }}>
                                        {{ $event->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('exam_event_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Bank Soal</label>
                            <select name="bank_soal_id" class="form-select @error('bank_soal_id') is-invalid @enderror" required>
                                <option value="">Pilih Bank Soal...</option>
                                @foreach($bankSoals as $bs)
                                    <option value="{{ $bs->id }}">{{ $bs->name }} ({{ $bs->code }})</option>
                                @endforeach
                            </select>
                            @error('bank_soal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Jadwal / Judul Ujian</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Contoh: Ujian Matematika Sesi 1" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Waktu Mulai</label>
                            <input type="datetime-local" name="start_at" class="form-control @error('start_at') is-invalid @enderror" required>
                            @error('start_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Waktu Selesai</label>
                            <input type="datetime-local" name="end_at" class="form-control @error('end_at') is-invalid @enderror" required>
                            @error('end_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Durasi (Menit)</label>
                            <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" placeholder="Contoh: 60" required>
                            @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Token & Interval</label>
                            <div class="input-group">
                                <input type="text" name="token" class="form-control" placeholder="Token statis">
                                <input type="number" name="token_interval" class="form-control" placeholder="Ubah tiap (menit)">
                            </div>
                            <small class="text-muted mt-1 d-block">Isi kolom kedua dengan angka (misal: 15) untuk token dinamis.</small>
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-3 flex-wrap">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label fw-bold" for="is_active">Aktif</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="randomize_questions" id="randomize_questions" value="1" checked>
                                <label class="form-check-label fw-bold" for="randomize_questions">Acak Soal</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="randomize_options" id="randomize_options" value="1" checked>
                                <label class="form-check-label fw-bold" for="randomize_options">Acak Jawaban</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary" style="background-color: #3b31c1; border: none;">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
