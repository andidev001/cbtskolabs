@extends('layouts.admin')

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
                                <th class="text-center">Kelas/Grup</th>
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
                <h5 class="modal-title">Isi Berita Acara - <span id="modalScheduleName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formBeritaAcara" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ruang Ujian</label>
                        <input type="text" name="ruang" id="inputRuang" class="form-control" placeholder="Contoh: Ruang 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sesi Ujian</label>
                        <input type="text" name="sesi" id="inputSesi" class="form-control" placeholder="Contoh: Sesi 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Proktor / Teknisi</label>
                        <input type="text" name="proktor" id="inputProktor" class="form-control" placeholder="Nama Proktor" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pengawas</label>
                        <input type="text" name="pengawas" id="inputPengawas" class="form-control" placeholder="Nama Pengawas" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan Selama Ujian</label>
                        <textarea name="catatan" id="inputCatatan" class="form-control" rows="3">Ujian berjalan dengan tertib dan lancar.</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal PDF Viewer -->
<div class="modal fade" id="modalPdfViewer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PDF Berita Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 position-relative" style="min-height: 85vh;">
                <div id="pdfLoading" class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 1;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
                    <div class="mt-3 fw-bold text-secondary">Sedang memproses PDF...</div>
                    <div class="small text-muted">Mohon tunggu sebentar.</div>
                </div>
                <iframe id="pdfIframe" src="" style="width:100%; height:100%; min-height:85vh; border:none; position:relative; z-index:2; opacity:0; transition: opacity 0.3s;" onload="if(this.src && this.src !== window.location.href && this.src !== 'about:blank') { document.getElementById('pdfLoading').style.display='none'; this.style.opacity='1'; }"></iframe>
            </div>
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
        ajax: "{{ route('admin.berita-acara.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'name', name: 'name', className: 'fw-bold text-primary'},
            {data: 'event_name', name: 'examEvent.name'},
            {data: 'group_name', name: 'group_name', className: 'text-center'},
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

    $(document).on('click', '.btn-edit-ba', function() {
        let url = $(this).data('url');
        let schedule = $(this).data('schedule');
        
        $('#formBeritaAcara').attr('action', url);
        $('#modalScheduleName').text(schedule);
        
        $('#inputRuang').val($(this).data('ruang') || '');
        $('#inputSesi').val($(this).data('sesi') || '');
        $('#inputProktor').val($(this).data('proktor') || '');
        $('#inputPengawas').val($(this).data('pengawas') || '');
        $('#inputCatatan').val($(this).data('catatan') || 'Ujian berjalan dengan tertib dan lancar.');
    });
    
    $(document).on('click', '.btn-view-pdf', function() {
        let url = $(this).data('url');
        $('#pdfLoading').show();
        $('#pdfIframe').css('opacity', '0').attr('src', url);
    });
    
    $('#modalPdfViewer').on('hidden.bs.modal', function () {
        $('#pdfIframe').attr('src', 'about:blank').css('opacity', '0');
        $('#pdfLoading').show();
    });
});
</script>
@endpush

<style>
    .table thead th { border-top: none; font-size: 0.85rem; }
    .table tbody td { font-size: 0.85rem; }
</style>
@endsection

