@extends('layouts.admin')

@section('title', 'Anggota Grup: ' . $group->name)

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.group.index') }}">Data Grup</a></li>
            <li class="breadcrumb-item active">{{ $group->name }}</li>
        </ol>
    </nav>
</div>

{{-- Header --}}
<div class="card shadow-sm border-0 mb-4" style="border-left: 4px solid #3b31c1 !important;">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white"
                 style="width:48px;height:48px;background-color:#3b31c1;flex-shrink:0;">
                <i data-feather="users" style="width:22px;height:22px;"></i>
            </div>
            <div>
                <h5 class="mb-0 fw-bold">{{ $group->name }}</h5>
                <small class="text-muted">{{ count($members) }} anggota terdaftar</small>
            </div>
        </div>
        <a href="{{ route('admin.group.index') }}" class="btn btn-sm btn-outline-secondary">
            <i data-feather="arrow-left" style="width:14px;height:14px;" class="me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- LEFT: Form Tambah Anggota --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-bold mb-0"><i data-feather="user-plus" class="me-2 align-middle" style="width:16px;height:16px;color:#3b31c1;"></i> Tambah Peserta Baru</h6>
                <small class="text-muted">Buat akun peserta baru ke grup ini</small>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                        <i data-feather="check-circle" style="width:13px;height:13px;" class="me-1"></i>{{ session('success') }}
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.group.member.store', $group->id) }}" method="POST">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Nama peserta" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-1">Username (login) <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control form-control-sm" placeholder="Contoh: budi2024" value="{{ old('username') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-1">NISN</label>
                        <input type="text" name="nisn" class="form-control form-control-sm" placeholder="Opsional" value="{{ old('nisn') }}">
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-1">Jurusan</label>
                        <select name="jurusan_id" class="form-select form-select-sm">
                            <option value="">-- Pilih Jurusan --</option>
                            @foreach($jurusans as $j)
                                <option value="{{ $j->id }}" {{ old('jurusan_id') == $j->id ? 'selected' : '' }}>{{ $j->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-1">Password <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="password" name="password" id="pw" class="form-control" placeholder="Min. 4 karakter" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw','eyePw')">
                                <i data-feather="eye" id="eyePw" style="width:14px;height:14px;"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold mb-1">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-group input-group-sm">
                            <input type="password" name="password_confirmation" id="pw2" class="form-control" placeholder="Ulangi password" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePw('pw2','eyePw2')">
                                <i data-feather="eye" id="eyePw2" style="width:14px;height:14px;"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-sm btn-primary" style="background-color:#3b31c1;border:none;">
                            <i data-feather="plus" style="width:14px;height:14px;" class="me-1"></i> Tambah Peserta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: Daftar Anggota --}}
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold mb-0"><i data-feather="list" class="align-middle me-2" style="width:16px;height:16px;color:#3b31c1;"></i> Daftar Anggota</h6>
                    <small class="text-muted">Peserta yang bisa login ke halaman ujian</small>
                </div>
                <span class="badge px-3 py-2" style="background-color:#3b31c1;">{{ count($members) }} peserta</span>
            </div>
            <div class="card-body p-0">
                @if(count($members) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="40" class="text-center ps-3">No</th>
                                <th>Nama</th>
                                <th>No. Ujian</th>
                                <th>Username</th>
                                <th>Jurusan</th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                            <tr>
                                <td class="text-center align-middle ps-3 text-muted">{{ $loop->iteration }}</td>
                                <td class="align-middle fw-bold">{{ $member->name }}</td>
                                <td class="align-middle">
                                    <code class="bg-light px-2 py-1 rounded small">{{ $member->nomer_ujian ?? '-' }}</code>
                                </td>
                                <td class="align-middle small text-muted">{{ $member->username }}</td>
                                <td class="align-middle small text-muted">{{ $member->jurusan->name ?? '-' }}</td>
                                <td class="text-center align-middle">
                                    <div class="d-flex gap-1 justify-content-center">
                                        {{-- Keluarkan dari grup --}}
                                        <form action="{{ route('admin.group.member.destroy', [$group->id, $member->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="btn btn-sm btn-warning text-white px-2" onclick="return confirm('Keluarkan peserta dari grup ini?')" title="Keluarkan dari grup">
                                                <i data-feather="log-out" style="width:13px;height:13px;"></i>
                                            </button>
                                        </form>
                                        {{-- Hapus akun --}}
                                        <form action="{{ route('admin.group.member.destroy', [$group->id, $member->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn btn-sm btn-danger px-2" onclick="return confirm('Hapus akun peserta ini secara permanen?')" title="Hapus akun">
                                                <i data-feather="trash-2" style="width:13px;height:13px;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i data-feather="users" style="width:45px;height:45px;opacity:0.2;display:block;margin:0 auto 12px;"></i>
                    <p class="mb-0 fw-bold">Belum ada anggota</p>
                    <small>Tambahkan peserta baru menggunakan form di sebelah kiri.</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .breadcrumb-item a { color: #3b31c1; text-decoration: none; }
    .table thead th { font-size: 0.8rem; font-weight: 600; color: #6c757d; text-transform: uppercase; }
    .table tbody td { font-size: 0.85rem; vertical-align: middle; }
</style>
<script>
function togglePw(inputId, iconId) {
    const el = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (el.type === 'password') {
        el.type = 'text';
        icon.setAttribute('data-feather', 'eye-off');
    } else {
        el.type = 'password';
        icon.setAttribute('data-feather', 'eye');
    }
    feather.replace();
}
</script>
@endsection
