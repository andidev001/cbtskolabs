@extends('layouts.guru')

@section('title', 'Monitoring Peserta')

@section('content')
<div class="row">
    <div class="col-12">
        <a href="{{ route('guru.monitoring.index') }}" class="btn btn-sm btn-secondary mb-3">
            <i data-feather="arrow-left" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Kembali
        </a>
        
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Monitoring Peserta: {{ $schedule->name }}</strong></h4>
                        <p class="text-muted small mb-0">Total Soal: <strong>{{ $totalQuestions }}</strong> | Token: <strong>{{ $schedule->token ?? '-' }}</strong></p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <select id="groupFilter" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">-- Semua Kelas/Grup --</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        
                        <button class="btn btn-primary btn-sm" id="btnRefresh">
                            <i data-feather="refresh-cw" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Refresh Status
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" id="tableMonitoring">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>No. Peserta</th>
                                <th>Nama Siswa</th>
                                <th>Grup/Kelas</th>
                                <th class="text-center">Status</th>
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
</style>



@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var table = $('#tableMonitoring').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('guru.monitoring.show', $schedule->id) }}",
                data: function (d) {
                    d.group_id = $('#groupFilter').val();
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
                if(window.feather) {
                    feather.replace();
                }
            }
        });

        $('#groupFilter').on('change', function() {
            table.draw();
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
                title: 'Buka Blokir?',
                text: "Siswa dapat melanjutkan ujian kembali.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Buka!',
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
