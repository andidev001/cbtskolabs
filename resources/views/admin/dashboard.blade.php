@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

{{-- Stat Cards --}}
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius:20px;overflow:hidden;">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-feather="users" style="width:20px;height:20px;color:white;"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;line-height:1;">{{ $countSiswa }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Total Siswa</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius:20px;overflow:hidden;">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-feather="database" style="width:20px;height:20px;color:white;"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;line-height:1;">{{ $countBankSoal }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Bank Soal</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius:20px;overflow:hidden;">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#f59e0b,#d97706);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-feather="calendar" style="width:20px;height:20px;color:white;"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;line-height:1;">{{ $countEvent }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Event Ujian</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 card-hover" style="border-radius:20px;overflow:hidden;">
            <div class="card-body d-flex align-items-center gap-3 p-3">
                <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#ef4444,#dc2626);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-feather="activity" style="width:20px;height:20px;color:white;"></i>
                </div>
                <div>
                    <div style="font-size:1.4rem;font-weight:800;line-height:1;">{{ $countOngoing }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Jadwal Aktif</div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row mt-2">
    <div class="col-12 col-lg-8 col-xxl-9 d-flex">
        <div class="card flex-fill shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="card-title mb-0 fw-bold">Statistik Siswa per Grup</h5>
            </div>
            <div class="card-body">
                <div class="chart chart-lg">
                    <canvas id="chartjs-siswa-group"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4 col-xxl-3 d-flex">
        <div class="card flex-fill w-100 shadow-sm border-0">
            <div class="card-header bg-white border-0">
                <h5 class="card-title mb-0 fw-bold">Ujian Mendatang</h5>
            </div>
            <div class="card-body p-3">
                @forelse ($upcomingExams as $exam)
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <div class="bg-primary-soft text-primary rounded p-2 text-center" style="min-width: 50px;">
                            <div class="fw-bold h5 mb-0">{{ $exam->start_at->format('d') }}</div>
                            <div class="small fw-bold">{{ strtoupper($exam->start_at->translatedFormat('M')) }}</div>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0 fw-bold">{{ $exam->name }}</h6>
                        <small class="text-muted">{{ $exam->start_at->format('H:i') }} WIB</small>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <small>Tidak ada jadwal terdekat</small>
                </div>
                @endforelse
                <div class="d-grid mt-2">
                    <a href="{{ route('admin.exam-schedule.index') }}" class="btn btn-primary btn-sm rounded-3">Buka Semua Jadwal</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12">
        <div class="card flex-fill w-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="card-title mb-0 fw-bold">Sebaran Siswa per Jurusan</h5>
            </div>
            <div class="card-body">
                <div class="chart chart-md">
                    <canvas id="chartjs-siswa-jurusan"></canvas>
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
                    backgroundColor: "#1a4da1",
                    borderColor: "#1a4da1",
                    hoverBackgroundColor: "#0d3a85",
                    hoverBorderColor: "#0d3a85",
                    data: {!! json_encode($chartData['data']) !!},
                    barPercentage: .5,
                    categoryPercentage: .5
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    yAxes: [{
                        gridLines: { display: true, color: "rgba(0,0,0,0.05)", drawBorder: false },
                        ticks: { stepSize: 10, beginAtZero: true }
                    }],
                    xAxes: [{ gridLines: { display: false } }]
                }
            }
        });

        // Jurusan Chart
        new Chart(document.getElementById("chartjs-siswa-jurusan"), {
            type: "horizontalBar",
            data: {
                labels: {!! json_encode($jurusanChartData['labels']) !!},
                datasets: [{
                    label: "Jumlah Siswa",
                    backgroundColor: "#10b981",
                    borderColor: "#10b981",
                    data: {!! json_encode($jurusanChartData['data']) !!},
                    barPercentage: .5,
                    categoryPercentage: .5
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{
                        gridLines: { display: true, color: "rgba(0,0,0,0.05)", drawBorder: false },
                        ticks: { stepSize: 10, beginAtZero: true }
                    }],
                    yAxes: [{ gridLines: { display: false } }]
                }
            }
        });
    });
</script>

<style>
    .card-hover:hover { transform: translateY(-3px); transition: all 0.2s ease; }
    .bg-primary-light { background: rgba(26, 77, 161, 0.1) !important; }
    .bg-success-light { background: rgba(16, 185, 129, 0.1) !important; }
    .bg-info-light { background: rgba(59, 130, 246, 0.1) !important; }
    .bg-danger-light { background: rgba(239, 68, 68, 0.1) !important; }
    
    .bg-primary-soft { background: #eef2f6; }
    .bg-success-soft { background: #d1fae5; }
    .bg-warning-soft { background: #fef3c7; }
    .bg-danger-soft { background: #fee2e2; }
    
    .text-success { color: #10b981 !important; }
    .text-warning { color: #f59e0b !important; }
    .text-danger { color: #ef4444 !important; }
</style>
@endsection

