@extends('layouts.admin')

@section('title', 'Edit Peserta')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.peserta.index') }}" class="btn btn-secondary btn-sm">
        <i class="align-middle" data-feather="arrow-left"></i> Kembali
    </a>
</div>

<h1 class="h3 mb-3">Edit Peserta: {{ $siswa->name }}</h1>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.peserta.update', $siswa->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Ujian</label>
                    <input type="text" class="form-control @error('nomer_ujian') is-invalid @enderror" name="nomer_ujian" value="{{ old('nomer_ujian', $siswa->nomer_ujian) }}" required>
                    @error('nomer_ujian') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $siswa->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $siswa->username) }}" required>
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin ganti)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Agama</label>
                    <select class="form-select" name="religion_id">
                        <option value="">-- Pilih Agama --</option>
                        @foreach($religions as $religion)
                            <option value="{{ $religion->id }}" {{ old('religion_id', $siswa->religion_id) == $religion->id ? 'selected' : '' }}>{{ $religion->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jurusan</label>
                    <select class="form-select" name="jurusan_id">
                        <option value="">-- Pilih Jurusan --</option>
                        @foreach($jurusans as $jurusan)
                            <option value="{{ $jurusan->id }}" {{ old('jurusan_id', $siswa->jurusan_id) == $jurusan->id ? 'selected' : '' }}>{{ $jurusan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Grup / Kelas</label>
                    <select class="form-select" name="group_id">
                        <option value="">-- Pilih Grup --</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id', $siswa->group_id) == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Sesi Ujian</label>
                    <input type="text" class="form-control" name="sesi" value="{{ old('sesi', $siswa->sesi) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="1" {{ old('status', $siswa->status) == '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('status', $siswa->status) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-primary px-4">Update Peserta</button>
            </div>
        </form>
    </div>
</div>
@endsection
