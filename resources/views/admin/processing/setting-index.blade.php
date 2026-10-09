@extends('layouts.admin')

@section('title', 'Setting Pengolahan Ujian')

@section('content')
<div class="card shadow-sm border-0">
    <div class="card-body pt-4">
        <h4 class="mb-1"><strong>Setting Pengolahan Ujian</strong></h4>
        <p class="text-muted small mb-4">Pengaturan publikasi nilai dan akses siswa terhadap hasil ujian</p>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="alert-message">
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 w-100" id="setting-table">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Ujian</th>
                        <th>Event</th>
                        <th class="text-center">Tampilkan Nilai</th>
                        <th class="text-center">Persentase</th>
                        <th class="text-center">Jawaban</th>
                        <th class="text-center">Anti-Curang</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    $('#setting-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.processing.setting.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'name', name: 'name', className: 'fw-bold text-primary'},
            {data: 'event_name', name: 'examEvent.name'},
            {
                data: 'show_score', 
                name: 'show_score', 
                className: 'text-center',
                render: function(data, type, row) {
                    return '<div class="form-check form-switch d-inline-block">' +
                           '<input class="form-check-input toggle-setting" type="checkbox" data-id="' + row.id + '" data-field="show_score" ' + (data ? 'checked' : '') + '>' +
                           '</div>';
                }
            },
            {
                data: 'show_percentage', 
                name: 'show_percentage', 
                className: 'text-center',
                render: function(data, type, row) {
                    return '<div class="form-check form-switch d-inline-block">' +
                           '<input class="form-check-input toggle-setting" type="checkbox" data-id="' + row.id + '" data-field="show_percentage" ' + (data ? 'checked' : '') + '>' +
                           '</div>';
                }
            },
            {
                data: 'show_answer', 
                name: 'show_answer', 
                className: 'text-center',
                render: function(data, type, row) {
                    return '<div class="form-check form-switch d-inline-block">' +
                           '<input class="form-check-input toggle-setting" type="checkbox" data-id="' + row.id + '" data-field="show_answer" ' + (data ? 'checked' : '') + '>' +
                           '</div>';
                }
            },
            {
                data: 'enforce_focus', 
                name: 'enforce_focus', 
                className: 'text-center',
                render: function(data, type, row) {
                    return '<div class="form-check form-switch d-inline-block">' +
                           '<input class="form-check-input toggle-setting" type="checkbox" data-id="' + row.id + '" data-field="enforce_focus" ' + (data ? 'checked' : '') + '>' +
                           '</div><br><small class="text-muted" style="font-size: 0.6rem;">Keluarkan jika pindah tab</small>';
                }
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
        }
    });

    $(document).on('change', '.toggle-setting', function() {
        const $this = $(this);
        const id = $this.data('id');
        const field = $this.data('field');
        const value = $this.prop('checked') ? 1 : 0;
        
        // Disable temporarily to prevent double click
        $this.prop('disabled', true);
        
        $.ajax({
            url: "{{ route('admin.processing.setting.update', '') }}/" + id,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                [field]: value,
                is_ajax_toggle: 1
            },
            success: function(res) {
                // Show a small success toast if possible, or just re-enable
                $this.prop('disabled', false);
                
                // Visual feedback: pulse the switch
                $this.closest('.form-switch').fadeOut(100).fadeIn(100);
            },
            error: function() {
                alert('Gagal memperbarui pengaturan.');
                $this.prop('checked', !value); // Revert
                $this.prop('disabled', false);
            }
        });
    });
});
</script>
@endpush
@endsection
