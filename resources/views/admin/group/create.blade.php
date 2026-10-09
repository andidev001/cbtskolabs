@extends('layouts.admin')

@section('title', 'Tambah Grup')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.group.index') }}" class="btn btn-secondary btn-sm">
        <i class="align-middle" data-feather="arrow-left"></i> Kembali
    </a>
</div>

<h1 class="h3 mb-3">Tambah Grup Baru</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.group.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Grup / Kelas</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="Contoh: X RPL 1" required autofocus>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Masukkan nama grup atau kelas sebagai pengelompokan peserta.</div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">Simpan Grup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
