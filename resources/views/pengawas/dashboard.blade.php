@extends('layouts.pengawas')

@section('title', 'Dashboard Pengawas')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card text-white shadow-sm overflow-hidden" style="border-radius: 10px; background: linear-gradient(135deg, #4361ee, #3a0ca3);">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center">
                    <div class="col-md-8 position-relative z-1">
                        <h2 class="text-white mb-2 fw-bold">Selamat Datang, {{ auth('pengawas')->user()->name }}!</h2>
                        <p class="mb-0 fs-5 text-white-50">Sebagai pengawas, Anda memiliki tanggung jawab penuh untuk memantau jalannya ujian, memastikan token ujian dibagikan dengan benar, serta mencetak berita acara pelaksanaan.</p>
                    </div>
                    <div class="col-md-4 text-end d-none d-md-block position-relative z-1">
                        <i data-feather="shield" style="width: 100px; height: 100px; opacity: 0.2; position: absolute; right: 20px; top: -10px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Card Ujian Aktif -->
    <div class="col-12 col-md-6 col-xxl-3 d-flex">
        <div class="card flex-fill shadow-sm bg-white" style="border-left: 4px solid #3b7ddd; border-radius: 8px;">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h3 class="mb-1 fw-bold text-dark">Ujian Aktif</h3>
                        <p class="text-muted small mb-3">Total ujian yang berlangsung hari ini.</p>
                        <h2 class="mb-0 fw-bold text-primary">{{ $active_exams_count }}</h2>
                    </div>
                    <div class="d-inline-block ms-3">
                        <div class="stat text-primary bg-primary-light p-3 rounded text-center">
                            <i class="align-middle" data-feather="calendar" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Status Ujian -->
    <div class="col-12 col-md-6 col-xxl-3 d-flex">
        <div class="card flex-fill shadow-sm bg-white" style="border-left: 4px solid #6f42c1; border-radius: 8px;">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h3 class="mb-2 fw-bold text-dark">Status Ujian</h3>
                        <p class="text-muted small mb-3">Lihat dan kelola token ujian untuk diberikan kepada peserta.</p>
                        <a href="{{ route('pengawas.exam-status.index') }}" class="btn btn-outline-primary btn-sm stretched-link" style="border-color: #6f42c1; color: #6f42c1;">Kelola Token <i data-feather="arrow-right" class="ms-1" style="width: 14px; height:14px;"></i></a>
                    </div>
                    <div class="d-inline-block ms-3">
                        <div class="stat p-3 rounded text-center" style="background-color: rgba(111, 66, 193, 0.1); color: #6f42c1;">
                            <i class="align-middle" data-feather="activity" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Berita Acara -->
    <div class="col-12 col-md-6 col-xxl-3 d-flex">
        <div class="card flex-fill shadow-sm bg-white" style="border-left: 4px solid #17a2b8; border-radius: 8px;">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h3 class="mb-2 fw-bold text-dark">Berita Acara</h3>
                        <p class="text-muted small mb-3">Isi dan cetak laporan pelaksanaan ujian per kelas.</p>
                        <a href="{{ route('pengawas.berita-acara.index') }}" class="btn btn-outline-info btn-sm stretched-link">Cetak Laporan <i data-feather="arrow-right" class="ms-1" style="width: 14px; height:14px;"></i></a>
                    </div>
                    <div class="d-inline-block ms-3">
                        <div class="stat text-info bg-info-light p-3 rounded text-center" style="background-color: rgba(23, 162, 184, 0.1);">
                            <i class="align-middle" data-feather="printer" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card Monitoring -->
    <div class="col-12 col-md-6 col-xxl-3 d-flex">
        <div class="card flex-fill shadow-sm bg-white" style="border-left: 4px solid #28a745; border-radius: 8px;">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h3 class="mb-2 fw-bold text-dark">Monitoring</h3>
                        <p class="text-muted small mb-3">Pantau siswa, paksa selesai, atau buka blokir ujian siswa.</p>
                        <a href="{{ route('pengawas.monitoring.index') }}" class="btn btn-outline-success btn-sm stretched-link">Pantau Siswa <i data-feather="arrow-right" class="ms-1" style="width: 14px; height:14px;"></i></a>
                    </div>
                    <div class="d-inline-block ms-3">
                        <div class="stat text-success bg-success-light p-3 rounded text-center" style="background-color: rgba(40, 167, 69, 0.1);">
                            <i class="align-middle" data-feather="monitor" style="width: 24px; height: 24px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection