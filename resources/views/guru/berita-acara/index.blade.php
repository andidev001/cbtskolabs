@extends('layouts.guru')

@section('title', 'Berita Acara Ujian')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Berita Acara Ujian</strong></h4>
                        <p class="text-muted small mb-0">Pilih ujian untuk mencetak berita acara pelaksanaan.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 w-100" id="berita-acara-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th>Nama Ujian</th>
                                <th>Event</th>
                                <th class="text-center">Bank Soal</th>
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

@push('modals')
<!-- Modal Berita Acara -->
<div class="modal fade" id="modalBeritaAcara" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cetak Berita Acara - <span id="modalScheduleName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBeritaAcara" action="" method="GET" target="_blank">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ruang Ujian</label>
                        <input type="text" name="ruang" class="form-control" placeholder="Contoh: Ruang 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sesi Ujian</label>
                        <input type="text" name="sesi" class="form-control" placeholder="Contoh: Sesi 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Proktor / Teknisi</label>
                        <input type="text" name="proktor" class="form-control" placeholder="Nama Proktor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pengawas</label>
                        <input type="text" name="pengawas" class="form-control" placeholder="Nama Pengawas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Selama Ujian</label>
                        <textarea name="catatan" class="form-control" rows="3">Ujian berjalan dengan tertib dan lancar.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Cetak Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
$(function() {
    $('#berita-acara-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('guru.berita-acara.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'name', name: 'name', className: 'fw-bold text-primary'},
            {data: 'event_name', name: 'examEvent.name'},
            {data: 'bank_soal_name', name: 'bankSoal.name', className: 'text-center small text-muted'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });

    $(document).on('click', '.btn-print-ba', function() {
        let url = $(this).data('url');
        let schedule = $(this).data('schedule');
        
        $('#formBeritaAcara').attr('action', url);
        $('#modalScheduleName').text(schedule);
    });
});
</script>
@endpush

<style>
    .table thead th { border-top: none; font-size: 0.85rem; }
    .table tbody td { font-size: 0.85rem; }
</style>
@endsection

