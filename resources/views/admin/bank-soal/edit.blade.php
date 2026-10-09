@extends('layouts.admin')

@section('title', 'Edit Bank Soal')

@section('content')
<div class="mb-3">
    <h4 class="h4"><strong>Edit banksoal</strong></h4>
</div>

<div class="row">
    <div class="col-12">
        <form action="{{ route('admin.bank-soal.update', $bankSoal->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Mata pelajaran</label>
                        <select name="subject_id" class="form-select border-1 @error('subject_id') is-invalid @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id', $bankSoal->subject_id) == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Kode banksoal</label>
                        <input type="text" name="code" class="form-control border-1 @error('code') is-invalid @enderror" value="{{ old('code', $bankSoal->code) }}" required>
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Nama banksoal (Optional)</label>
                        <input type="text" name="name" class="form-control border-1 @error('name') is-invalid @enderror" value="{{ old('name', $bankSoal->name) }}" placeholder="Contoh: Paket Soal 1">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-0">
                        <label class="form-label text-muted d-block">Status</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" value="1" id="statusSwitch" {{ old('status', $bankSoal->status) ? 'checked' : '' }}>
                            <label class="form-check-label text-muted small" for="statusSwitch">Aktif / Non-Aktif</label>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $types = [
                    'pg' => ['label' => 'Pilihan ganda'],
                    'pgk' => ['label' => 'Pilihan ganda kompleks'],
                    'ls' => ['label' => 'Listening'],
                    'mj' => ['label' => 'Menjodohkan'],
                    'is' => ['label' => 'Isian singkat'],
                    'ur' => ['label' => 'Uraian'],
                    'mt' => ['label' => 'Mengurutkan'],
                    'bs' => ['label' => 'Benar/Salah'],
                    'st' => ['label' => 'Setuju/Tidak'],
                ];
            @endphp

            @foreach($types as $key => $type)
                @php
                    $countVal = $bankSoal->{$key . '_count'};
                    $percentVal = $bankSoal->{$key . '_percent'};
                    $isExpanded = $countVal > 0 || $key == 'pg';
                @endphp
                <div class="card border-info mb-2 question-type-card {{ $isExpanded ? 'active' : '' }}">
                    <div class="card-header bg-white border-0 py-2 d-flex justify-content-between align-items-center cursor-pointer" 
                         onclick="toggleCard(this)" data-target="body-{{ $key }}">
                        <div class="d-flex align-items-center text-primary">
                            <i class="align-middle me-2 chevron-icon" data-feather="{{ $isExpanded ? 'chevron-down' : 'chevron-right' }}"></i>
                            <span>{{ $type['label'] }}</span>
                        </div>
                    </div>
                    <div id="body-{{ $key }}" class="card-body pt-0 {{ $isExpanded ? '' : 'd-none' }}">
                        <hr class="mt-0 mb-3">
                        <h6 class="fw-bold mb-3">{{ $type['label'] }}</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted">Jumlah soal</label>
                                <div class="input-group">
                                    <input type="number" name="{{ $key }}_count" class="form-control" value="{{ old($key . '_count', $countVal) }}">
                                    <button class="btn btn-outline-secondary bg-light border-start-0" type="button">
                                        <i class="align-middle" data-feather="plus" style="width: 14px;"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small text-muted">Persen</label>
                                <div class="input-group">
                                    <input type="number" name="{{ $key }}_percent" class="form-control" value="{{ old($key . '_percent', $percentVal) }}">
                                    <button class="btn btn-outline-secondary bg-light border-start-0" type="button">
                                        <i class="align-middle" data-feather="plus" style="width: 14px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-end gap-2 mt-4 mb-5">
                <button type="submit" class="btn btn-primary px-4" style="background-color: #3b31c1;">Simpan Perubahan</button>
                <a href="{{ route('admin.bank-soal.index') }}" class="btn btn-light px-4 border">Cancel</a>
            </div>


        </form>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .question-type-card {
        border: 1px solid #cce5ff;
        border-radius: 4px;
        transition: all 0.2s;
    }
    .question-type-card.active {
        border-color: #007bff;
        box-shadow: 0 0 0 1px #007bff;
    }
    .text-primary { color: #007bff !important; }
    .form-label { font-weight: 500; margin-bottom: 0.25rem; }
    .card-header:hover { background-color: #f8f9fa !important; }
    .input-group .btn { 
        padding-left: 0.75rem; 
        padding-right: 0.75rem; 
        color: #6c757d;
        border-color: #dee2e6;
    }
    .input-group .form-control { border-right: 0; }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
</style>

<script>
    function toggleCard(header) {
        const targetId = header.getAttribute('data-target');
        const body = document.getElementById(targetId);
        const card = header.closest('.question-type-card');
        const icon = header.querySelector('.chevron-icon');
        
        if (body.classList.contains('d-none')) {
            body.classList.remove('d-none');
            card.classList.add('active');
            icon.innerHTML = feather.icons['chevron-down'].toSvg();
        } else {
            body.classList.add('d-none');
            card.classList.remove('active');
            icon.innerHTML = feather.icons['chevron-right'].toSvg();
        }
        feather.replace();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection
