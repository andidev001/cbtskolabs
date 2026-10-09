@extends('layouts.guru')

@section('title', 'Koreksi Siswa: ' . $siswa->name)

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('guru.processing.koreksi.show', $schedule->id) }}" class="btn btn-sm btn-secondary mb-3">
            <i data-feather="arrow-left" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Kembali ke Daftar Peserta
        </a>
        
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1"><strong>Koreksi Siswa: {{ $siswa->name }}</strong></h4>
                        <p class="text-muted small mb-0">No. Ujian: {{ $siswa->nomer_ujian }} | Jadwal: {{ $schedule->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('guru.processing.koreksi.store', [$schedule->id, $siswa->id]) }}" method="POST">
            @csrf
            
            @forelse($questions as $index => $q)
                @php
                    $ans = $answers->get($q->id);
                @endphp
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="fw-bold mb-0">Soal No. {{ $index + 1 }}</h5>
                            <span class="badge bg-secondary">{{ $q->type == 'UR' ? 'Uraian' : 'Esai' }}</span>
                        </div>
                        
                        <div class="p-3 bg-light rounded mb-3 border">
                            {!! $q->question_text !!}
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Jawaban Siswa:</label>
                            <div class="p-3 bg-white rounded border" style="min-height: 80px; white-space: pre-wrap;">{{ $ans->answer ?? '<Kosong>' }}</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold text-success">Kunci Jawaban / Referensi:</label>
                            <div class="p-3 bg-white rounded border" style="min-height: 50px; white-space: pre-wrap;">{{ $q->answer ?? '<Tidak ada kunci jawaban>' }}</div>
                        </div>
                        
                        <hr>
                        
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-bold mb-0">Nilai (0 - 100):</label>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="scores[{{ $q->id }}]" class="form-control" min="0" max="100" value="{{ $ans->score ?? 0 }}" required>
                            </div>
                            <div class="col-md-6 text-muted small">
                                * Isi dengan skala 0 hingga 100.
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    Tidak ada soal esai atau uraian pada jadwal ujian ini.
                </div>
            @endforelse

            @if($questions->count() > 0)
            <div class="card shadow-sm border-0">
                <div class="card-body text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i data-feather="save" class="align-middle me-1"></i> Simpan Nilai
                    </button>
                </div>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection

