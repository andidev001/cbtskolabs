@extends('layouts.guru')

@section('title', 'Dashboard Guru')

@section('content')
@php $guru = auth('guru')->user(); @endphp

<div style="background: linear-gradient(135deg, #1a4da1 0%, #0d3a85 100%); padding: 32px 28px; border-radius: 18px; margin-bottom: 28px; color: white; position: relative; overflow: hidden;">
    <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,0.05);border-radius:50%;"></div>
    <div style="position:absolute;bottom:-60px;right:60px;width:150px;height:150px;background:rgba(255,255,255,0.04);border-radius:50%;"></div>
    <div style="position:relative;z-index:1;">
        <div style="font-size:0.85rem;opacity:0.8;margin-bottom:6px;">Selamat datang kembali 👋</div>
        <h2 style="font-size:1.8rem;font-weight:800;margin:0 0 6px;">{{ $guru->name }}</h2>
        <div style="font-size:0.9rem;opacity:0.75;">
            @if($guru->nip) NIP: {{ $guru->nip }} &nbsp;|&nbsp; @endif
            Mata Pelajaran: <strong>{{ $guru->subjectNames() ?: 'Belum dipetakan' }}</strong>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden;">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-feather="database" style="width:24px;height:24px;color:white;"></i>
                </div>
                <div>
                    <div style="font-size:1.8rem;font-weight:800;line-height:1;">{{ $totalBankSoal }}</div>
                    <div class="text-muted" style="font-size:0.82rem;">Bank Soal</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden;">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-feather="clock" style="width:24px;height:24px;color:white;"></i>
                </div>
                <div>
                    <div style="font-size:1.8rem;font-weight:800;line-height:1;">{{ $totalJadwal }}</div>
                    <div class="text-muted" style="font-size:0.82rem;">Jadwal Ujian</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden;">
            <div class="card-body d-flex align-items-center gap-3 p-4">
                <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-feather="award" style="width:24px;height:24px;color:white;"></i>
                </div>
                <div>
                    <div style="font-size:1.8rem;font-weight:800;line-height:1;">{{ $totalNilai }}</div>
                    <div class="text-muted" style="font-size:0.82rem;">Hasil Ujian</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Quick Access --}}
<h5 class="fw-bold mb-3">Akses Cepat</h5>
<div class="row g-3">
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('guru.bank-soal.create') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="border-radius:14px; border-left:4px solid #3b82f6 !important;">
            <div class="card-body p-4">
                <i data-feather="plus-circle" class="text-primary mb-2" style="width:28px;height:28px;"></i>
                <div class="fw-semibold">Buat Bank Soal</div>
                <small class="text-muted">Tambah bank soal baru</small>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('guru.bank-soal.index') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="border-radius:14px; border-left:4px solid #8b5cf6 !important;">
            <div class="card-body p-4">
                <i data-feather="list" class="text-primary mb-2" style="width:28px;height:28px; color:#8b5cf6 !important;"></i>
                <div class="fw-semibold">Kelola Soal</div>
                <small class="text-muted">Lihat & input soal</small>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('guru.processing.koreksi.index') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="border-radius:14px; border-left:4px solid #f59e0b !important;">
            <div class="card-body p-4">
                <i data-feather="check-square" style="width:28px;height:28px;color:#f59e0b;" class="mb-2"></i>
                <div class="fw-semibold">Koreksi Esai</div>
                <small class="text-muted">Nilai jawaban uraian</small>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-lg-3">
        <a href="{{ route('guru.processing.hasil.index') }}" class="card border-0 shadow-sm text-decoration-none h-100" style="border-radius:14px; border-left:4px solid #10b981 !important;">
            <div class="card-body p-4">
                <i data-feather="bar-chart-2" style="width:28px;height:28px;color:#10b981;" class="mb-2"></i>
                <div class="fw-semibold">Hasil Ujian</div>
                <small class="text-muted">Lihat rekap nilai siswa</small>
            </div>
        </a>
    </div>
</div>
@endsection
