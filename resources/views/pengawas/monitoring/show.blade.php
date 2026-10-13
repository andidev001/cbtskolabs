@extends('layouts.pengawas')

@section('title', 'Monitoring Peserta')

@section('content')
<div class="row">
    <div class="col-12">
        <a href="{{ route('pengawas.monitoring.index') }}" class="btn btn-sm btn-secondary mb-3">
            <i data-feather="arrow-left" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Kembali
        </a>
        
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Monitoring Peserta: {{ $schedule->name }}</strong></h4>
                        <p class="text-muted small mb-0">Total Soal: <strong>{{ $totalQuestions }}</strong> | Token: <strong>{{ $schedule->token ?? '-' }}</strong></p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 align-items-center">
                        <select id="groupFilter" class="form-select form-select-sm" style="width: auto; min-width: 180px;">
                            <option value="">-- Semua Kelas/Grup --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>

                        <select id="statusFilter" class="form-select form-select-sm" style="width: auto; min-width: 170px;">
                            <option value="">-- Semua Status --</option>
                            <option value="ongoing">Sedang Mengerjakan</option>
                            <option value="finished">Selesai</option>
                            <option value="blocked">⚠️ Pelanggaran / Diblokir</option>
                            <option value="not_started">Belum Mulai</option>
                        </select>
                        
                        <button class="btn btn-primary btn-sm text-nowrap d-inline-flex align-items-center" id="btnRefresh">
                            <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i> Refresh Status
                        </button>
                    </div>
                </div>

                <!-- Ringkasan Status & Filter Cepat -->
                <div class="row g-2 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="card border border-light-subtle shadow-none p-2 mb-0 stat-filter-card active" data-filter="">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-muted fw-semibold">Semua Peserta</span>
                                <span class="badge bg-secondary rounded-pill" id="stat-total">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border border-light-subtle shadow-none p-2 mb-0 stat-filter-card" data-filter="ongoing">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-primary fw-semibold">Sedang Ujian</span>
                                <span class="badge bg-primary rounded-pill" id="stat-ongoing">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border border-light-subtle shadow-none p-2 mb-0 stat-filter-card" data-filter="finished">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-success fw-semibold">Selesai</span>
                                <span class="badge bg-success rounded-pill" id="stat-finished">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card border border-danger-subtle bg-danger bg-opacity-10 shadow-none p-2 mb-0 stat-filter-card" data-filter="blocked" id="card-blocked">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="small text-danger fw-bold"><i data-feather="alert-triangle" style="width: 13px; height: 13px;" class="me-1"></i> Pelanggaran</span>
                                <span class="badge bg-danger rounded-pill" id="stat-blocked">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert Banner jika ada siswa terblokir -->
                <div id="alert-blocked-banner" class="alert alert-danger d-none align-items-center justify-content-between py-2 px-3 mb-3" role="alert">
                    <div class="d-flex align-items-center gap-2">
                        <i data-feather="alert-octagon" class="text-danger flex-shrink-0" style="width: 20px; height: 20px;"></i>
                        <div>
                            <strong>Perhatian Pengawas:</strong> Ada <span id="alert-blocked-count" class="badge bg-danger">0</span> siswa terkunci karena terdeteksi melakukan pelanggaran ujian!
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger text-nowrap py-1 px-2 d-inline-flex align-items-center" id="btn-show-blocked">
                        <i data-feather="eye" style="width: 13px; height: 13px;" class="me-1"></i> Lihat Siswa Terblokir
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" id="tableMonitoring">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>No. Peserta</th>
                                <th>Nama Siswa</th>
                                <th>Grup/Kelas</th>
                                <th class="text-center" width="160">Status</th>
                                <th class="text-center">Progres</th>
                                <th class="text-center">Skor</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th { border-top: none; font-size: 0.82rem; }
    .table tbody td { font-size: 0.82rem; }
    .badge { font-weight: 500; font-size: 0.70rem; padding: 3px 8px; }
    .progress { background-color: #e9ecef; border-radius: 10px; }
    .stat-filter-card {
        cursor: pointer;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
    }
    .stat-filter-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .stat-filter-card.active {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 2px rgba(13,110,253,0.25);
    }
    #card-blocked.active {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 2px rgba(220,53,69,0.25);
    }
</style>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        function updateStats(json) {
            if (!json) return;
            var total = json.totalCount !== undefined ? json.totalCount : 0;
            var ongoing = json.ongoingCount !== undefined ? json.ongoingCount : 0;
            var finished = json.finishedCount !== undefined ? json.finishedCount : 0;
            var blocked = json.blockedCount !== undefined ? json.blockedCount : 0;

            $('#stat-total').text(total);
            $('#stat-ongoing').text(ongoing);
            $('#stat-finished').text(finished);
            $('#stat-blocked').text(blocked);

            if (blocked > 0) {
                $('#alert-blocked-banner').removeClass('d-none').addClass('d-flex');
                $('#alert-blocked-count').text(blocked);
            } else {
                $('#alert-blocked-banner').removeClass('d-flex').addClass('d-none');
            }
        }

        var table = $('#tableMonitoring').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pengawas.monitoring.show', $schedule->id) }}",
                data: function (d) {
                    d.group_id = $('#groupFilter').val();
                    d.status_filter = $('#statusFilter').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center align-middle' },
                { data: 'nomer_ujian', name: 'nomer_ujian', className: 'align-middle' },
                { data: 'name', name: 'name', className: 'align-middle fw-bold' },
                { data: 'group_name', name: 'group_name', className: 'align-middle' },
                { data: 'status', name: 'status', className: 'text-center align-middle', orderable: false, searchable: false },
                { data: 'progress', name: 'progress', className: 'align-middle', orderable: false, searchable: false },
                { data: 'score', name: 'score', className: 'text-center align-middle', orderable: false, searchable: false },
                { data: 'action', name: 'action', className: 'align-middle text-center', orderable: false, searchable: false }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
            },
            drawCallback: function(settings) {
                try {
                    if(window.feather) {
                        feather.replace();
                    }
                } catch(e) {
                    console.warn(e);
                }

                if (settings.json) {
                    updateStats(settings.json);
                }
            }
        });

        $('#tableMonitoring').on('xhr.dt', function(e, settings, json, xhr) {
            if (json) {
                updateStats(json);
            }
        });

        function syncActiveCard(status) {
            $('.stat-filter-card').removeClass('active');
            $('.stat-filter-card[data-filter="' + status + '"]').addClass('active');
        }

        $('#groupFilter').on('change', function() {
            table.ajax.reload(null, true);
        });

        $('#statusFilter').on('change', function() {
            syncActiveCard($(this).val());
            table.ajax.reload(null, true);
        });

        $('.stat-filter-card').on('click', function() {
            var filter = $(this).data('filter');
            $('#statusFilter').val(filter);
            syncActiveCard(filter);
            table.ajax.reload(null, true);
        });

        $('#btn-show-blocked').on('click', function() {
            $('#statusFilter').val('blocked');
            syncActiveCard('blocked');
            table.ajax.reload(null, true);
        });

        $('#btnRefresh').on('click', function() {
            table.ajax.reload(null, false);
        });

        // SweetAlert Handlers
        $('#tableMonitoring').on('click', '.btn-force-finish', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Paksa Selesai?',
                text: "Ujian siswa ini akan diakhiri secara paksa!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesai!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('#tableMonitoring').on('click', '.btn-unblock', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Buka Blokir Siswa?',
                text: "Siswa akan diizinkan login kembali dan melanjutkan ujian.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Buka Blokir!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('#tableMonitoring').on('click', '.btn-reset', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Reset Total Ujian?',
                text: "Semua jawaban siswa akan dihapus dan ujian diulang dari awal!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        $('#tableMonitoring').on('click', '.btn-reset-login', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Reset Login?',
                text: "Status login siswa akan direset dan siswa bisa login kembali dari perangkat lain.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        // Auto refresh setiap 30 detik
        setInterval(function() {
            table.ajax.reload(null, false);
        }, 30000);
    });
</script>
@endpush
@endsection
