@extends('layouts.admin')

@section('title', 'Master Pengaturan Sesi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 fw-bold mb-0">Master Pengaturan Sesi</h1>
        <p class="text-muted small mb-0">Atur jam mulai dan selesai untuk masing-masing Sesi (1, 2, 3, dsb)</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSesi">
        <i data-feather="plus" class="align-middle me-1"></i> Tambah Sesi
    </button>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="dataTable" class="table table-hover mb-0 w-100">
                <thead class="bg-light">
                    <tr>
                        <th width="50" class="text-center">No</th>
                        <th>Nama Sesi</th>
                        <th class="text-center">Jam Mulai</th>
                        <th class="text-center">Jam Selesai</th>
                        <th width="150" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $sesi)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle fw-bold">
                            <div class="fw-bold"><i data-feather="clock" class="me-2 text-muted" style="width:14px;height:14px;"></i>Sesi {{ $sesi->name }}</div>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge rounded-pill bg-light text-dark border">{{ \Carbon\Carbon::parse($sesi->start_time)->format('H:i') }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge rounded-pill bg-light text-dark border">{{ \Carbon\Carbon::parse($sesi->end_time)->format('H:i') }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <button class="btn btn-sm btn-primary text-white" data-bs-toggle="modal" data-bs-target="#modalEditSesi{{ $sesi->id }}" title="Edit Sesi" data-bs-toggle="tooltip">
                                <i data-feather="edit-2"></i>
                            </button>
                            <form action="{{ route('admin.exam-session.destroy', $sesi->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger btn-delete" title="Hapus Sesi" data-bs-toggle="tooltip">
                                    <i data-feather="trash-2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('modals')
@foreach($sessions as $sesi)
<!-- Edit Modal -->
<div class="modal fade" id="modalEditSesi{{ $sesi->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.exam-session.update', $sesi->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sesi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sesi</label>
                        <input type="text" name="name" class="form-control" value="{{ $sesi->name }}" placeholder="Contoh: 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="start_time" class="form-control" value="{{ \Carbon\Carbon::parse($sesi->start_time)->format('H:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="end_time" class="form-control" value="{{ \Carbon\Carbon::parse($sesi->end_time)->format('H:i') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Add Modal -->
<div class="modal fade" id="modalAddSesi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.exam-session.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Sesi Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Sesi (Angka/Huruf)</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: 1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Mulai</label>
                        <input type="time" name="start_time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jam Selesai</label>
                        <input type="time" name="end_time" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            }
        });
    });
</script>
@endpush
