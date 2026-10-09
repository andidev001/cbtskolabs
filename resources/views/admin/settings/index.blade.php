@extends('layouts.admin')

@section('title', 'Informasi Instansi')

@section('content')
<h1 class="h3 mb-3">Informasi Instansi</h1>

<div class="row">
    <div class="col-md-4 col-xl-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Logo Instansi</h5>
            </div>
            <div class="card-body text-center">
                @if($settings['logo'])
                    <img src="{{ asset($settings['logo']) }}" alt="Logo" class="img-fluid rounded-circle mb-2" width="128" height="128" />
                @else
                    <img src="{{ asset('assets/adminkit/img/avatars/avatar.jpg') }}" alt="Default Logo" class="img-fluid rounded-circle mb-2" width="128" height="128" />
                @endif
                <div class="text-muted mb-2">Logo akan muncul pada halaman login dan kop laporan</div>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-xl-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detail Informasi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Instansi</label>
                            <input type="text" class="form-control" name="nama_instansi" value="{{ $settings['nama_instansi'] }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Instansi</label>
                            <input type="email" class="form-control" name="email" value="{{ $settings['email'] }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="2">{{ $settings['alamat'] }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" class="form-control" name="telepon" value="{{ $settings['telepon'] }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload Logo Baru</label>
                            <input type="file" class="form-control" name="logo">
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Pejabat Berwenang</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kepala Sekolah</label>
                            <input type="text" class="form-control" name="kepala_sekolah" value="{{ $settings['kepala_sekolah'] }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIP Kepala Sekolah</label>
                            <input type="text" class="form-control" name="nip_kepala_sekolah" value="{{ $settings['nip_kepala_sekolah'] }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenjang Sekolah</label>
                            <select class="form-select" name="jenjang">
                                <option value="" disabled {{ !$settings['jenjang'] ? 'selected' : '' }}>Pilih Jenjang...</option>
                                <option value="SD" {{ $settings['jenjang'] == 'SD' ? 'selected' : '' }}>SD / MI</option>
                                <option value="SMP" {{ $settings['jenjang'] == 'SMP' ? 'selected' : '' }}>SMP / MTs</option>
                                <option value="SMA" {{ $settings['jenjang'] == 'SMA' ? 'selected' : '' }}>SMA / MA</option>
                                <option value="SMK" {{ $settings['jenjang'] == 'SMK' ? 'selected' : '' }}>SMK</option>
                                <option value="Perguruan Tinggi" {{ $settings['jenjang'] == 'Perguruan Tinggi' ? 'selected' : '' }}>Perguruan Tinggi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Sekolah</label>
                            <select class="form-select" name="status">
                                <option value="" disabled {{ !$settings['status'] ? 'selected' : '' }}>Pilih Status...</option>
                                <option value="Negeri" {{ $settings['status'] == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                                <option value="Swasta" {{ $settings['status'] == 'Swasta' ? 'selected' : '' }}>Swasta</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Backup & Restore Database Section -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0 fw-bold"><i class="align-middle me-2" data-feather="database" style="color: #3b31c1;"></i> Backup & Restore Database</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <!-- Backup Column -->
                    <div class="col-md-6 border-end">
                        <h6 class="fw-bold">Backup Database</h6>
                        <p class="text-muted small mb-3">Unduh salinan (backup) database aplikasi saat ini secara keseluruhan. Sangat disarankan untuk rutin melakukan backup agar terhindar dari kehilangan data.</p>
                        <a href="{{ route('admin.settings.backup') }}" class="btn btn-success">
                            <i class="align-middle me-1" data-feather="download"></i> Download File SQL
                        </a>
                    </div>
                    
                    <!-- Restore Column -->
                    <div class="col-md-6">
                        <h6 class="fw-bold">Restore Database</h6>
                        <p class="text-muted small mb-3">Pulihkan (restore) data dari file backup <code>.sql</code> yang telah Anda unduh sebelumnya. <strong class="text-danger">Peringatan:</strong> Proses ini akan menghapus dan menimpa database saat ini!</p>
                        <form action="{{ route('admin.settings.restore') }}" method="POST" enctype="multipart/form-data" class="d-inline">
                            @csrf
                            <div class="input-group">
                                <input type="file" class="form-control" name="backup_file" accept=".sql" required>
                                <button class="btn btn-danger btn-restore" type="button" onclick="confirmRestore(this.form)">
                                    <i class="align-middle me-1" data-feather="upload"></i> Restore Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmRestore(form) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: 'Mereset & menimpa seluruh database! Pastikan file backup sudah benar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan Restore!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
</script>

@endsection

