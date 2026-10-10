@extends('layouts.guru')

@section('title', 'Edit Profil')

@section('content')
<h1 class="h3 mb-3">Pengaturan Profil</h1>

<div class="row">
    <div class="col-md-4 col-xl-3">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Foto Profil</h5>
            </div>
            <div class="card-body text-center">
                @if(auth('guru')->user()->photo)
                    <img src="{{ asset(auth('guru')->user()->photo) }}" alt="" class="img-fluid rounded-circle mb-2" width="128" height="128" style="object-fit: cover;" onerror="this.onerror=null; this.outerHTML='<div class=\'d-inline-flex align-items-center justify-content-center text-white rounded-circle shadow-sm mb-2\' style=\'width:128px;height:128px; font-weight:bold; font-size:3rem; background: linear-gradient(135deg, #4361ee, #3a0ca3);\'>{{ strtoupper(substr(auth('guru')->user()->name, 0, 1)) }}</div>';" />
                @else
                    <img src="{{ asset('assets/adminkit/img/avatars/avatar.jpg') }}" alt="Default Avatar" class="img-fluid rounded-circle mb-2" width="128" height="128" />
                @endif
                <h5 class="card-title mb-0">{{ auth('guru')->user()->name }}</h5>
                <div class="text-muted mb-2">Guru / Pengajar</div>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-xl-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Pribadi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user->username) }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Ganti Foto Profil</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" name="photo">
                            @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mb-3">Ganti Password <small class="text-muted text-sm">(Kosongkan jika tidak ingin mengubah)</small></h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="password_confirmation">
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

