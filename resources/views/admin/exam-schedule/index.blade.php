@extends('layouts.admin')

@section('title', 'Manage Jadwal ujian')

@section('content')
<div class="mb-3">
    <button type="button" class="btn btn-primary" style="background-color: #3b31c1;" data-bs-toggle="modal" data-bs-target="#createModal">
        Tambah jadwal
    </button>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body pt-4">
        <h4 class="mb-1"><strong>Manage Jadwal ujian</strong></h4>
        <p class="text-muted small mb-4">Buat dan aktifkan jadwal ujian</p>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 w-100" id="schedule-table">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Event</th>
                        <th>Bank Soal</th>
                        <th>Nama Ujian</th>
                        <th>Waktu (Mulai - Selesai)</th>
                        <th>Durasi</th>
                        <th class="text-center">Status</th>
                        <th width="100" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createModalLabel">Tambah Jadwal Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.exam-schedule.store') }}" method="POST">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Event Ujian</label>
                            <select name="exam_event_id" class="form-select" required>
                                <option value="">Pilih Event...</option>
                                @foreach($examEvents as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Bank Soal</label>
                            <select name="bank_soal_id" class="form-select" required>
                                <option value="">Pilih Bank Soal...</option>
                                @foreach($bankSoals as $bs)
                                    <option value="{{ $bs->id }}">{{ $bs->name }} ({{ $bs->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Jadwal / Judul Ujian</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Ujian Matematika Sesi 1" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Waktu Mulai</label>
                            <input type="datetime-local" name="start_at" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Waktu Selesai</label>
                            <input type="datetime-local" name="end_at" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Durasi (Menit)</label>
                            <input type="number" name="duration" class="form-control" placeholder="Contoh: 60" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Token (Opsional)</label>
                            <input type="text" name="token" class="form-control" placeholder="Kosongkan jika tidak pakai token">
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-3 flex-wrap">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                                <label class="form-check-label fw-bold" for="is_active">Aktif</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="randomize_questions" id="randomize_questions" value="1" checked>
                                <label class="form-check-label fw-bold" for="randomize_questions">Acak Soal</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="randomize_options" id="randomize_options" value="1" checked>
                                <label class="form-check-label fw-bold" for="randomize_options">Acak Jawaban</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #3b31c1; border: none;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">Edit Jadwal Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Event Ujian</label>
                            <select name="exam_event_id" id="edit_exam_event_id" class="form-select" required>
                                <option value="">Pilih Event...</option>
                                @foreach($examEvents as $event)
                                    <option value="{{ $event->id }}">{{ $event->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Bank Soal</label>
                            <select name="bank_soal_id" id="edit_bank_soal_id" class="form-select" required>
                                <option value="">Pilih Bank Soal...</option>
                                @foreach($bankSoals as $bs)
                                    <option value="{{ $bs->id }}">{{ $bs->name }} ({{ $bs->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Jadwal / Judul Ujian</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Waktu Mulai</label>
                            <input type="datetime-local" name="start_at" id="edit_start_at" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Waktu Selesai</label>
                            <input type="datetime-local" name="end_at" id="edit_end_at" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Durasi (Menit)</label>
                            <input type="number" name="duration" id="edit_duration" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small fw-bold">Token (Opsional)</label>
                            <input type="text" name="token" id="edit_token" class="form-control" placeholder="Kosongkan jika tidak pakai token">
                        </div>
                        <div class="col-md-6 d-flex align-items-end gap-3 flex-wrap">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                                <label class="form-check-label fw-bold" for="edit_is_active">Aktif</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="randomize_questions" id="edit_randomize_questions" value="1">
                                <label class="form-check-label fw-bold" for="edit_randomize_questions">Acak Soal</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="randomize_options" id="edit_randomize_options" value="1">
                                <label class="form-check-label fw-bold" for="edit_randomize_options">Acak Jawaban</label>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #3b31c1; border: none;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function() {
    document.body.appendChild(document.getElementById('createModal'));
    document.body.appendChild(document.getElementById('editModal'));

    $('#schedule-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.exam-schedule.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center'},
            {data: 'event_name', name: 'examEvent.name'},
            {data: 'bank_soal_name', name: 'bankSoal.name'},
            {data: 'name', name: 'name', className: 'fw-bold text-primary'},
            {data: 'timing', name: 'start_at'},
            {
                data: 'duration', 
                name: 'duration', 
                className: 'text-center',
                render: function(data) {
                    return data + ' mnt';
                }
            },
            {data: 'status_label', name: 'is_active', className: 'text-center'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        drawCallback: function() {
            feather.replace();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        let exam_event_id = $(this).data('exam_event_id');
        let bank_soal_id = $(this).data('bank_soal_id');
        let name = $(this).data('name');
        let start_at = $(this).data('start_at');
        let end_at = $(this).data('end_at');
        let duration = $(this).data('duration');
        let is_active = $(this).data('is_active');
        let randomize_questions = $(this).data('randomize_questions');
        let randomize_options = $(this).data('randomize_options');
        let token = $(this).data('token');

        $('#editForm').attr('action', `/admin/exam-schedule/${id}`);
        $('#edit_exam_event_id').val(exam_event_id);
        $('#edit_bank_soal_id').val(bank_soal_id);
        $('#edit_name').val(name);
        $('#edit_start_at').val(start_at);
        $('#edit_end_at').val(end_at);
        $('#edit_duration').val(duration);
        $('#edit_token').val(token);
        
        if(is_active == 1) {
            $('#edit_is_active').prop('checked', true);
        } else {
            $('#edit_is_active').prop('checked', false);
        }

        if(randomize_questions == 1 || randomize_questions === undefined) {
            $('#edit_randomize_questions').prop('checked', true);
        } else {
            $('#edit_randomize_questions').prop('checked', false);
        }

        if(randomize_options == 1 || randomize_options === undefined) {
            $('#edit_randomize_options').prop('checked', true);
        } else {
            $('#edit_randomize_options').prop('checked', false);
        }

        var editModal = new bootstrap.Modal(document.getElementById('editModal'));
        editModal.show();
    });
});
</script>
@endpush

<style>
    .table thead th { border-top: none; font-size: 0.85rem; }
    .table tbody td { font-size: 0.85rem; }
    .badge { font-weight: 500; font-size: 0.75rem; }
</style>
@endsection
