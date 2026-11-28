@extends('layouts.admin')

@section('title', 'Bank Soal')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-2">
        <div class="row align-items-center">
            <div class="col-auto">
                <h5 class="card-title mb-0"><strong>Bank Soal</strong></h5>
            </div>
            <div class="col text-end">
                <a href="{{ route('admin.bank-soal.create') }}" class="btn btn-primary btn-sm">
                    <i class="align-middle" data-feather="plus"></i> Tambah Bank Soal
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped table-hover w-100 mb-0" id="bank-soal-table">
                <thead>
                    <tr>
                        <th width="40">No</th>
                        <th>Kode</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru Pengampu</th>
                        <th>Nama Bank Soal</th>
                        <th class="text-center">Jumlah Soal</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="185">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Salin / Copy Bank Soal -->
<div class="modal fade" id="copyBankModal" tabindex="-1" aria-labelledby="copyBankModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="copyBankModalLabel">
                    <i class="align-middle me-1" data-feather="copy"></i> Salin Bank Soal ke Guru Lain
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="copyBankForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2 px-3 small mb-3 border-0" style="background-color: #eef5ff; color: #1967d2;">
                        <div><strong>Sumber:</strong> <span id="copySourceInfo">-</span></div>
                        <div><strong>Mata Pelajaran:</strong> <span id="copySubjectInfo">-</span> (<span id="copyQuestionCount">0</span> butir soal akan ikut disalin)</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Salin untuk Guru Pengampu</label>
                        <select name="guru_id" id="copy-guru-select" class="form-select">
                            <option value="">-- Semua Guru Mapel Ini (Umum / Admin) --</option>
                            @foreach($gurus ?? [] as $guru)
                                <option value="{{ $guru->id }}">
                                    {{ $guru->name }} @if($guru->nip) ({{ $guru->nip }}) @endif
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 0.74rem;">Pilih guru tujuan yang akan menerima salinan Bank Soal ini.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Kode Bank Soal Baru</label>
                        <input type="text" name="code" id="copy_code" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold">Nama Bank Soal Baru</label>
                        <input type="text" name="name" id="copy_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm px-3" style="background-color: #3b31c1; border: none;">
                        <i class="align-middle me-1" data-feather="copy" style="width: 14px; height: 14px;"></i> Salin Bank Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(function() {
    document.body.appendChild(document.getElementById('copyBankModal'));

    $('#copy-guru-select').select2({
        theme: 'bootstrap-5',
        width: '100%',
        dropdownParent: $('#copyBankModal'),
        placeholder: '-- Semua Guru Mapel Ini (Umum / Admin) --',
        allowClear: true
    }).on('select2:open', function() {
        document.querySelector('.select2-container--open .select2-search__field')?.setAttribute('placeholder', 'Ketik nama atau NIP guru...');
    });

    $(document).on('click', '.btn-copy-bank', function() {
        var id = $(this).data('id');
        var code = $(this).data('code');
        var name = $(this).data('name');
        var subject = $(this).data('subject');
        var qCount = $(this).data('questions');

        var randSuffix = Math.floor(100 + Math.random() * 900);
        $('#copyBankForm').attr('action', "{{ url('admin/bank-soal') }}/" + id + "/duplicate");
        $('#copySourceInfo').text(name + ' (' + code + ')');
        $('#copySubjectInfo').text(subject);
        $('#copyQuestionCount').text(qCount);
        $('#copy_code').val(code + '-CP' + randSuffix);
        $('#copy_name').val(name + ' (Salinan)');
        $('#copy-guru-select').val('').trigger('change');

        var modal = new bootstrap.Modal(document.getElementById('copyBankModal'));
        modal.show();
    });
    $('#bank-soal-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.bank-soal.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {
                data: 'code', 
                name: 'code',
                render: function(data) {
                    return '<span class="badge bg-info">' + data + '</span>';
                }
            },
            {data: 'subject_name', name: 'subject.name'},
            {data: 'guru_name', name: 'guru.name', defaultContent: '-'},
            {
                data: 'name', 
                name: 'name',
                className: 'fw-bold'
            },
            {
                data: 'questions_count', 
                name: 'questions_count',
                searchable: false,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return '<span class="badge bg-secondary">' + data + ' / ' + row.pg_count + '</span>';
                }
            },
            {data: 'status_label', name: 'status', orderable: false, searchable: false, className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });

    $(document).on('change', '.toggle-status', function() {
        var id = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var switchElement = $(this);
        
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        $.ajax({
            url: "{{ url('admin/bank-soal') }}/" + id + "/toggle-status",
            type: "PATCH",
            data: {
                _token: "{{ csrf_token() }}",
                status: isChecked ? 1 : 0
            },
            success: function(response) {
                Toast.fire({
                    icon: 'success',
                    title: response.message || 'Status diperbarui'
                });
            },
            error: function() {
                // Revert switch if failed
                switchElement.prop('checked', !isChecked);
                Toast.fire({
                    icon: 'error',
                    title: 'Gagal memperbarui status'
                });
            }
        });
    });
});
</script>
@endpush

<style>
    .card-body { padding: 0.75rem !important; }
    .table > :not(caption) > * > * { padding: 0.4rem 0.5rem !important; }
    .btn-sm { padding: 0.2rem 0.4rem; font-size: 0.8rem; }
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 33px !important;
        padding: 0.25rem 0.65rem !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.82rem !important;
        color: #495057 !important;
        border: 1px solid #dee2e6 !important;
        border-radius: 5px !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        font-size: 0.82rem !important;
        color: #495057 !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option {
        padding: 0.28rem 0.65rem !important;
        font-size: 0.82rem !important;
    }
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option--highlighted,
    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options .select2-results__option--selected {
        background-color: #1967d2 !important;
        color: #ffffff !important;
    }
</style>
@endsection
