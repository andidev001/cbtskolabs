@extends('layouts.admin')

@section('title', 'Detail Hasil: ' . $schedule->name)

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <a href="{{ route('admin.processing.hasil.index') }}" class="btn btn-sm btn-secondary mb-3">
            <i data-feather="arrow-left" class="align-middle me-1" style="width: 14px; height: 14px;"></i> Kembali
        </a>
        
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">
                        <strong>Berhasil!</strong> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Hasil Ujian: {{ $schedule->name }}</strong></h4>
                        <p class="text-muted small mb-0">{{ $schedule->examEvent->name }} | {{ $schedule->bankSoal->name }}</p>
                    </div>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <form action="{{ route('admin.processing.hasil.reset', $schedule->id) }}" method="POST" id="form-delete-all" class="d-inline m-0">
                            @csrf
                            <button type="button" class="btn btn-outline-danger btn-sm text-nowrap d-inline-flex align-items-center" id="btn-delete-all">
                                <i data-feather="trash-2" class="me-1" style="width: 14px; height: 14px;"></i> Hapus Semua Hasil
                            </button>
                        </form>
                        <form action="{{ route('admin.processing.hasil.recalculate', $schedule->id) }}" method="POST" id="form-recalculate" class="d-inline m-0">
                            @csrf
                            <button type="button" class="btn btn-outline-primary btn-sm text-nowrap d-inline-flex align-items-center" id="btn-recalculate" onclick="if(confirm('Kalkulasi ulang nilai semua siswa yang telah selesai ujian ini?')) this.form.submit();">
                                <i data-feather="refresh-cw" class="me-1" style="width: 14px; height: 14px;"></i> Hitung Ulang Nilai
                            </button>
                        </form>
                        <select name="group_id" class="form-select form-select-sm" style="width: auto; min-width: 140px;">
                            <option value="">Semua Kelas</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('admin.processing.hasil.export-excel', ['id' => $schedule->id, 'group_id' => request('group_id')]) }}" id="exportBtn" class="btn btn-success btn-sm text-nowrap d-inline-flex align-items-center">
                            <i data-feather="download" class="me-1" style="width: 14px; height: 14px;"></i> Export Excel
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" id="tableHasil">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>No. Peserta</th>
                                <th>Nama Siswa</th>
                                <th>Grup/Kelas</th>
                                <th class="text-center">Benar</th>
                                <th class="text-center">Salah</th>
                                <th class="text-center">Skor</th>
                                <th class="text-center">Status</th>
                                <th width="80" class="text-center">Aksi</th>
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
    .table thead th { border-top: none; font-size: 0.85rem; }
    .table tbody td { font-size: 0.85rem; }
    .badge { font-weight: 500; font-size: 0.70rem; padding: 3px 8px; }
</style>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        let table = $('#tableHasil').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.processing.hasil.show', $schedule->id) }}",
                data: function(d) {
                    d.group_id = $('select[name="group_id"]').val();
                }
            },
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
                {data: 'nomer_ujian', name: 'siswa.nomer_ujian'},
                {data: 'nama_siswa', name: 'siswa.name'},
                {data: 'grup_kelas', name: 'siswa.group.name'},
                {data: 'benar', name: 'correct_answers', className: 'text-center'},
                {data: 'salah', name: 'salah', searchable: false, orderable: false, className: 'text-center'},
                {data: 'skor', name: 'score', className: 'text-center'},
                {data: 'status', name: 'status', searchable: false, orderable: false, className: 'text-center'},
                {data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center'}
            ],
            order: [[6, 'desc']],
            drawCallback: function(settings) {
                if(window.feather) {
                    feather.replace();
                }
            }
        });

        $('select[name="group_id"]').on('change', function() {
            table.ajax.reload();
            
            // Update export link URL dynamically
            let url = new URL($('#exportBtn').prop('href'));
            if($(this).val()) {
                url.searchParams.set('group_id', $(this).val());
            } else {
                url.searchParams.delete('group_id');
            }
            $('#exportBtn').attr('href', url.toString());
        });

        // SweetAlert for Hapus Semua Hasil
        $('#btn-delete-all').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus SEMUA Hasil?',
                text: "Tindakan ini akan menghapus semua hasil ujian pada jadwal ini dan tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#form-delete-all').submit();
                }
            });
        });

        // SweetAlert for Hapus Hasil Siswa
        $('#tableHasil').on('click', '.btn-delete-hasil', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Hasil Ujian?',
                text: "Hasil ujian siswa ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>
@endpush
