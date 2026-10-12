@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.03);
        border: 1px solid rgba(255,255,255,0.8);
    }
    .welcome-bg {
        position: absolute;
        right: -20px;
        bottom: -20px;
        opacity: 0.7;
        width: 300px;
        transition: transform 0.5s ease;
    }
    .welcome-card:hover .welcome-bg {
        transform: scale(1.03) translate(-3px, -3px);
    }
    .stat-card {
        border-radius: 20px;
        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03);
        border: 1px solid rgba(241, 245, 249, 0.8);
        transition: all 0.3s ease;
        background: #fff;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px -5px rgba(0,0,0,0.06);
    }
    .icon-box {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }
    .stat-card:hover .icon-box {
        transform: scale(1.05);
    }
    .smooth-card {
        border-radius: 20px !important;
        box-shadow: 0 4px 20px -2px rgba(0,0,0,0.03) !important;
        border: 1px solid rgba(241, 245, 249, 0.8) !important;
        transition: all 0.3s ease !important;
        background: #fff;
    }
    .smooth-card:hover {
        box-shadow: 0 12px 25px -5px rgba(0,0,0,0.06) !important;
    }
    .text-success-custom { color: #10b981; }
    .text-primary-custom { color: #3b82f6; }
</style>

<div class="row g-4 mb-4">
    <div class="col-12 col-xl-8">
        <div class="welcome-card p-4 h-100 d-flex align-items-center">
            <div style="z-index: 1;">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width:40px; height:40px; background:#4361ee !important;">
                        <i data-feather="book-open" style="width:20px; height:20px;"></i>
                    </div>
                    <span class="text-muted fw-bold">Selamat Datang,</span>
                </div>
                <h2 class="fw-bolder mb-2" style="color: #1e293b; font-size: 1.8rem;">{{ auth('admin')->user()->name }}</h2>
                <p class="text-muted mb-0" style="max-width: 450px; font-size:0.9rem;">
                    Kelola dan pantau seluruh data dan aktivitas sistem CBT SKOLABS {{ \App\Models\Setting::get('nama_instansi', 'Kab. Tangerang') }} dengan mudah dan cepat.
                </p>
            </div>
            <!-- Illustration placeholder, could use an SVG or image -->
            <svg class="welcome-bg d-none d-md-block" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="right: -10px; bottom: 0px; width: 180px; opacity: 1;">
                <path fill="#e0e7ff" d="M47.7,-57.2C59.4,-47.3,64.8,-29.3,66.8,-11.7C68.8,5.9,67.5,23,59.3,37.3C51,51.6,35.9,63.1,18.8,68.6C1.7,74.1,-17.4,73.5,-32.8,66C-48.2,58.4,-60,43.8,-67.2,26.7C-74.4,9.6,-77,-10,-70.5,-26.4C-64.1,-42.9,-48.7,-56,-32.8,-63.3C-16.8,-70.5,-0.3,-71.9,16,-68.8C32.4,-65.7,47.7,-57.2,47.7,-57.2Z" transform="translate(100 100)" />
                <rect x="50" y="50" width="100" height="70" rx="5" fill="#4361ee" />
                <rect x="55" y="55" width="90" height="50" rx="3" fill="#fff" />
                <circle cx="100" cy="80" r="15" fill="#e0e7ff" />
                <path d="M90 80 L97 87 L115 70" stroke="#4361ee" stroke-width="3" fill="none" />
                <rect x="70" y="120" width="60" height="5" rx="2" fill="#94a3b8" />
            </svg>
        </div>
    </div>
    
    <div class="col-12 col-xl-4">
        <div class="stat-card p-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex align-items-center mb-3">
                <div class="icon-box" style="background: rgba(100, 116, 139, 0.1); color: #64748b; width:40px; height:40px;">
                    <i data-feather="calendar" style="width:18px;"></i>
                </div>
                <div class="ms-3">
                    <div class="fw-bold text-dark" style="font-size: 1.1rem;">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</div>
                    <div class="text-muted" style="font-size: 0.85rem;">{{ \Carbon\Carbon::now()->format('H:i') }} WIB</div>
                </div>
            </div>
            
            <div class="mt-3 p-3 rounded" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                <div class="d-flex align-items-center">
                    <span class="bg-success rounded-circle me-2" style="width: 10px; height: 10px; display:inline-block;"></span>
                    <span class="text-success fw-bold" style="font-size: 0.95rem;">Sistem Berjalan Normal</span>
                </div>
                <div class="text-muted ms-3 mt-1" style="font-size: 0.8rem; padding-left:4px;">Semua layanan aktif</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="icon-box text-white" style="background: #4361ee;">
                    <i data-feather="users" style="width: 18px; height: 18px;"></i>
                </div>
                <i data-feather="trending-up" class="text-primary-custom" style="width:16px; height:16px;"></i>
            </div>
            <div class="text-muted fw-bold mb-1" style="font-size:0.85rem;">Total Siswa</div>
            <h3 class="fw-bolder text-dark mb-1" style="font-size:1.6rem;">{{ $countSiswa }}</h3>
            <div class="text-success-custom" style="font-size:0.75rem; font-weight:600;"><i data-feather="arrow-up" style="width:12px;"></i> Data Terkini</div>
        </div>
    </div>
    
    <div class="col-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="icon-box text-white" style="background: #10b981;">
                    <i data-feather="database" style="width: 18px; height: 18px;"></i>
                </div>
                <i data-feather="file-text" class="text-muted" style="width:16px; height:16px;"></i>
            </div>
            <div class="text-muted fw-bold mb-1" style="font-size:0.85rem;">Bank Soal</div>
            <h3 class="fw-bolder text-dark mb-1" style="font-size:1.6rem;">{{ $countBankSoal }}</h3>
            <div class="text-muted" style="font-size:0.75rem; font-weight:500;">— Tidak ada perubahan</div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="icon-box text-white" style="background: #f59e0b;">
                    <i data-feather="calendar" style="width: 18px; height: 18px;"></i>
                </div>
                <i data-feather="calendar" class="text-muted" style="width:16px; height:16px;"></i>
            </div>
            <div class="text-muted fw-bold mb-1" style="font-size:0.85rem;">Event Ujian</div>
            <h3 class="fw-bolder text-dark mb-1" style="font-size:1.6rem;">{{ $countEvent }}</h3>
            <div class="text-muted" style="font-size:0.75rem; font-weight:500;">— Tidak ada perubahan</div>
        </div>
    </div>

    <div class="col-6 col-xl-3">
        <div class="stat-card p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="icon-box text-white" style="background: #ef4444;">
                    <i data-feather="activity" style="width: 18px; height: 18px;"></i>
                </div>
                <i data-feather="trending-up" class="text-primary-custom" style="width:16px; height:16px;"></i>
            </div>
            <div class="text-muted fw-bold mb-1" style="font-size:0.85rem;">Jadwal Aktif</div>
            <h3 class="fw-bolder text-dark mb-1" style="font-size:1.6rem;">{{ $countOngoing }}</h3>
            <div class="text-success-custom" style="font-size:0.75rem; font-weight:600;"><i data-feather="arrow-up" style="width:12px;"></i> Real-time</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card h-100 border-0 smooth-card">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="bg-primary-soft text-primary rounded p-2 me-2">
                        <i data-feather="bar-chart-2" style="width:18px;"></i>
                    </div>
                    <h5 class="card-title mb-0 fw-bold" style="color:#1e293b;">Statistik Siswa per Grup</h5>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" style="border-radius:20px; font-weight:600; padding:4px 12px;">
                        Semua Grup
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Semua Grup</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body px-4 pb-4">
                <div class="chart chart-lg" style="height: 220px;">
                    <canvas id="chartjs-siswa-group"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card h-100 border-0 smooth-card">
            <div class="card-header bg-white border-0 pt-4 pb-0 px-4 d-flex align-items-center">
                <div class="bg-primary-soft text-primary rounded p-2 me-2" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6 !important;">
                    <i data-feather="clock" style="width:18px;"></i>
                </div>
                <h5 class="card-title mb-0 fw-bold" style="color:#1e293b;">Jadwal Ujian Terdekat</h5>
            </div>
            <div class="card-body p-4 d-flex flex-column">
                @if(count($upcomingExams) > 0)
                    @foreach ($upcomingExams as $exam)
                    @php
                        $isOngoing = isset($now) ? $now->between($exam->start_at, $exam->end_at) : false;
                    @endphp
                    <div class="d-flex align-items-center mb-3 p-3 rounded" style="background:#f8fafc; border:1px solid #f1f5f9;">
                        <div class="flex-shrink-0">
                            <div class="bg-white text-primary border rounded text-center" style="min-width: 55px; box-shadow:0 2px 4px rgba(0,0,0,0.02);">
                                <div class="small fw-bold text-muted border-bottom py-1" style="font-size:0.7rem; background:#f8fafc;">{{ strtoupper($exam->start_at->translatedFormat('M')) }}</div>
                                <div class="fw-bolder h4 mb-0 py-1" style="color:#4361ee;">{{ $exam->start_at->format('d') }}</div>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <h6 class="mb-0 fw-bold text-dark">{{ $exam->name }}</h6>
                                @if($isOngoing)
                                    <span class="badge bg-success" style="font-size:0.65rem;">Berlangsung</span>
                                @else
                                    <span class="badge bg-primary" style="font-size:0.65rem;">Mendatang</span>
                                @endif
                            </div>
                            <div class="small text-muted d-flex align-items-center">
                                <i data-feather="clock" style="width:12px; margin-right:4px;"></i> 
                                {{ $exam->start_at->format('H:i') }} - {{ $exam->end_at->format('H:i') }} WIB
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="flex-grow-1 d-flex flex-column justify-content-center align-items-center text-center py-4">
                        <div style="background: #f8fafc; width: 120px; height: 120px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:1rem;">
                            <i data-feather="calendar" style="width:50px; height:50px; color:#cbd5e1;"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">Tidak ada jadwal terdekat</h6>
                        <p class="text-muted small px-3">Saat ini tidak ada ujian aktif atau yang dijadwalkan.</p>
                    </div>
                @endif
                <div class="mt-auto">
                    <a href="{{ route('admin.exam-schedule.index') }}" class="btn btn-primary w-100 fw-bold" style="border-radius:10px; padding:0.6rem; background:#4361ee; border:none;">
                        <i data-feather="calendar" style="width:16px; margin-right:6px;"></i> Buka Semua Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Group Chart
        new Chart(document.getElementById("chartjs-siswa-group"), {
            type: "bar",
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: "Jumlah Siswa",
                    backgroundColor: "#4361ee",
                    borderColor: "#4361ee",
                    hoverBackgroundColor: "#3a0ca3",
                    borderRadius: 6,
                    data: {!! json_encode($chartData['data']) !!},
                    barPercentage: 0.5,
                    categoryPercentage: 0.5
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{
                        gridLines: { display: true, color: "#f1f5f9", drawBorder: false, zeroLineColor: "#f1f5f9" },
                        ticks: { stepSize: 10, beginAtZero: true, fontColor: "#94a3b8", padding: 10 }
                    }],
                    xAxes: [{ 
                        gridLines: { display: false, drawBorder: false },
                        ticks: { fontColor: "#64748b", fontStyle: "600" }
                    }]
                }
            }
        });
    });
</script>

<style>
    .bg-primary-soft { background: rgba(67, 97, 238, 0.1) !important; color: #4361ee !important; }
</style>
@endsection
