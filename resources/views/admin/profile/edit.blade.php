@extends('layouts.admin')

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
                @if(auth('admin')->user()->photo)
                    <img src="{{ asset(auth('admin')->user()->photo) }}" alt="{{ auth('admin')->user()->name }}" class="img-fluid rounded-circle mb-2" width="128" height="128" />
                @else
                    <img src="{{ asset('assets/adminkit/img/avatars/avatar.jpg') }}" alt="Default Avatar" class="img-fluid rounded-circle mb-2" width="128" height="128" />
                @endif
                <h5 class="card-title mb-0">{{ auth('admin')->user()->name }}</h5>
                <div class="text-muted mb-2">Administrator</div>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-xl-9">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informasi Pribadi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user->username) }}" required>
                            @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
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
