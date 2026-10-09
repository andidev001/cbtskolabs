@extends('layouts.guru')

@section('title', 'Edit Bank Soal')

@section('content')
<div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('guru.bank-soal.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-feather="arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Edit Bank Soal</h1>
</div>

<div class="row">
    <div class="col-12">
        <form action="{{ route('guru.bank-soal.update', $bankSoal->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mata Pelajaran <span class="text-danger">*</span></label>
                            <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                <option value="">-- Pilih --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $bankSoal->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kode Bank Soal <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code', $bankSoal->code) }}" required>
                            @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Bank Soal <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $bankSoal->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch"
                                       {{ old('status', $bankSoal->status) ? 'checked' : '' }}>
                                <label class="form-check-label" for="statusSwitch">Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $types = ['pg'=>'Pilihan Ganda','pgk'=>'PG Kompleks','ls'=>'Listening',
                          'mj'=>'Menjodohkan','is'=>'Isian Singkat','ur'=>'Uraian','mt'=>'Mengurutkan',
                          'bs'=>'Benar/Salah','st'=>'Setuju/Tidak'];
            @endphp

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-semibold">Konfigurasi Jumlah & Bobot Soal</div>
                <div class="card-body p-4">
                    <div class="row g-2 fw-semibold text-muted small mb-2">
                        <div class="col-md-4">Tipe Soal</div>
                        <div class="col-md-4">Jumlah Soal</div>
                        <div class="col-md-4">Bobot (%)</div>
                    </div>
                    @foreach($types as $key => $label)
                    <div class="row g-2 align-items-center mb-2">
                        <div class="col-md-4"><label class="form-label mb-0">{{ $label }}</label></div>
                        <div class="col-md-4">
                            <input type="number" name="{{ $key }}_count" class="form-control form-control-sm"
                                   value="{{ old($key.'_count', $bankSoal->{$key.'_count'}) }}" min="0">
                        </div>
                        <div class="col-md-4">
                            <div class="input-group input-group-sm">
                                <input type="number" name="{{ $key }}_percent" class="form-control"
                                       value="{{ old($key.'_percent', $bankSoal->{$key.'_percent'}) }}" min="0" max="100" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i data-feather="save" class="me-1"></i> Simpan Perubahan</button>
                <a href="{{ route('guru.bank-soal.show', $bankSoal->id) }}" class="btn btn-info text-white">
                    <i data-feather="list" class="me-1"></i> Lihat Soal
                </a>
                <a href="{{ route('guru.bank-soal.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
