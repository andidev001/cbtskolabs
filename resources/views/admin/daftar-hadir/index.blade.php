@extends('layouts.admin')

@section('title', 'Daftar Hadir Ujian')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body pt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-1"><strong>Daftar Hadir Ujian</strong></h4>
                        <p class="text-muted small mb-0">Pilih ujian untuk mencetak daftar hadir peserta.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 w-100" id="daftar-hadir-table">
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
<!-- Modal PDF Viewer -->
<div class="modal fade" id="modalPdfViewer" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PDF Daftar Hadir</h5>
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
    $('#daftar-hadir-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.daftar-hadir.index') }}",
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