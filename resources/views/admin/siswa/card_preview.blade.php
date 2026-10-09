@extends('layouts.admin')

@section('title', 'Preview Kartu Peserta')

@section('content')
<!-- Header & Breadcrumbs -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><strong>Cetak Kartu Peserta</strong></h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="list-style: none; padding: 0; display: flex; gap: 0.5rem; margin: 0; font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><span class="text-muted">/</span></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.peserta.index') }}" class="text-decoration-none text-muted">Peserta</a></li>
                <li class="breadcrumb-item"><span class="text-muted">/</span></li>
                <li class="breadcrumb-item active fw-bold text-primary">Cetak Kartu</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.peserta.index') }}" class="btn btn-secondary btn-sm">
        <i class="align-middle" data-feather="arrow-left"></i> Kembali
    </a>
</div>

<!-- Filter Section -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="card-title mb-0"><i class="align-middle me-2 text-primary" data-feather="filter"></i> <strong>Filter Cetak Kartu</strong></h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.peserta.card-preview') }}" method="GET" class="row align-items-end g-3">
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Grup / Kelas</label>
                <select name="group_id" class="form-select form-select-sm" onchange="this.form.action='{{ route('admin.peserta.card-preview') }}'; this.form.submit();">
                    <option value="">Semua Grup</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold">Jurusan</label>
                <select name="jurusan_id" class="form-select form-select-sm" onchange="this.form.action='{{ route('admin.peserta.card-preview') }}'; this.form.submit();">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusans as $jurusan)
                        <option value="{{ $jurusan->id }}" {{ request('jurusan_id') == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold">Judul Kartu Ujian</label>
                <input type="text" name="title" class="form-control form-control-sm" value="{{ request('title', $title) }}" placeholder="Misal: UJIAN SEKOLAH 2026">
            </div>

            <div class="col-md-2 text-end">
                <button type="submit" name="print" value="1" formtarget="_blank" class="btn btn-primary btn-sm w-100 shadow-sm" onclick="this.form.action='{{ route('admin.peserta.print-cards') }}';">
                    <i class="align-middle" data-feather="printer"></i> Cetak Kartu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Container -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-muted"><strong>Preview Kartu ({{ $siswas->total() ?? count($siswas) }} Peserta)</strong></h5>
    @if(method_exists($siswas, 'links'))
    <div>
        {{ $siswas->appends(request()->all())->links('pagination::bootstrap-4') }}
    </div>
    @endif
</div>

<div class="row">
    @forelse($siswas as $siswa)
        <div class="col-lg-6 mb-4">
            <div class="card h-100" style="border: 1px solid #000 !important; border-radius: 0 !important; box-shadow: none !important; color: #000 !important; font-family: Arial, Helvetica, sans-serif; background-color: #fff !important;">
                
                <!-- Card Header -->
                <div class="d-flex justify-content-between align-items-center p-2" style="border-bottom: 1px solid #000 !important;">
                    <div style="width: 55px;" class="text-center">
                        @php
                            $logoUrl = isset($setting['logo']) && $setting['logo'] ? asset($setting['logo']) : asset('assets/adminkit/img/avatars/avatar.jpg');
                        @endphp
                        <img src="{{ $logoUrl }}" style="max-width: 100%; filter: grayscale(100%);" alt="Logo">
                    </div>
                    <div class="text-center flex-grow-1 px-2" style="line-height: 1.2;">
                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.8rem;">KARTU LOGIN PESERTA</h6>
                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.85rem;">{{ mb_strtoupper(request('title', $title)) }}</h6>
                    </div>
                    <div style="width: 55px; height: 55px; border: 1px solid #000; display: flex; align-items: center; justify-content: center; font-size: 0.5rem; text-align: center;">
                        <i data-feather="grid"></i>
                    </div>
                </div>
                
                <!-- Card Body -->
                <div class="p-2 bg-white" style="font-size: 0.75rem; line-height: 1.5; color: #000;">
                    <table class="table table-borderless table-sm mb-2" style="color: #000; margin-bottom: 0.5rem !important;">
                        <tr>
                            <td class="p-0 pb-1" width="130">Nama Peserta</td>
                            <td class="p-0 pb-1 px-1" width="5">:</td>
                            <td class="p-0 pb-1"><strong>{{ mb_strtoupper($siswa->name) }}</strong></td>
                        </tr>
                        <tr>
                            <td class="p-0 pb-1">NISN / No Ujian</td>
                            <td class="p-0 pb-1 px-1">:</td>
                            <td class="p-0 pb-1">{{ $siswa->nisn ?? $siswa->nomer_ujian }}</td>
                        </tr>
                        <tr>
                            <td class="p-0 pb-1">Jurusan</td>
                            <td class="p-0 pb-1 px-1">:</td>
                            <td class="p-0 pb-1">{{ $siswa->jurusan ? $siswa->jurusan->name : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="p-0 pb-1">Ruang / Kelas</td>
                            <td class="p-0 pb-1 px-1">:</td>
                            <td class="p-0 pb-1">{{ $siswa->group ? $siswa->group->name : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="p-0 pb-1">Username</td>
                            <td class="p-0 pb-1 px-1">:</td>
                            <td class="p-0 pb-1"><strong style="font-size: 0.8rem;">{{ $siswa->username }}</strong></td>
                        </tr>
                        <tr>
                            <td class="p-0 pb-1">Password</td>
                            <td class="p-0 pb-1 px-1">:</td>
                            <td class="p-0 pb-1"><strong style="font-size: 0.8rem;">{{ $siswa->password_raw ?? '******' }}</strong></td>
                        </tr>
                    </table>

                    <!-- Photo & Schedule Box -->
                    <div class="d-flex mt-1">
                        <div style="width: 80px; height: 110px; min-width: 80px; border: 1px solid #000; display: flex; align-items: center; justify-content: center; background: transparent; color: #000;">
                            <span style="font-size: 0.7rem; font-weight: bold;">FOTO 3x4</span>
                        </div>
                        <div class="flex-grow-1 ms-2" style="border: 1px solid #000;">
                            <div class="text-center" style="background: #e5e7eb; border-bottom: 1px solid #000; padding: 3px 0;">
                                <strong style="font-size: 0.7rem;">CATATAN PENTING</strong>
                            </div>
                            <ul class="ps-3 mb-0 py-1" style="font-size: 0.65rem; padding-right: 10px; margin-top: 5px;">
                                <li style="margin-bottom: 5px;">Kartu ini wajib dibawa saat pelaksanaan ujian berlangsung.</li>
                                <li style="margin-bottom: 5px;">Jaga kerahasiaan Username dan Password Anda.</li>
                                <li style="margin-bottom: 5px;">Login hanya dapat dilakukan pada ruang dan gelombang yang telah ditentukan.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center text-muted py-5">
            <i data-feather="users" class="mb-2" style="width: 40px; height: 40px; opacity: 0.5;"></i>
            <p>Tidak ada data peserta untuk ditampilkan.</p>
        </div>
    @endforelse
</div>

<script>
    // Pastikan tombol Cetak Kartu diarahkan ke route cetak, bukan preview
    document.querySelector('button[name="print"]').addEventListener('click', function() {
        this.form.action = "{{ route('admin.peserta.print-cards') }}";
    });
</script>
@endsection
