@extends('layouts.admin')

@section('title', 'Tambah Jurusan')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.jurusan.index') }}" class="btn btn-secondary btn-sm"><i class="align-middle" data-feather="arrow-left"></i> Kembali</a>
</div>
<h1 class="h3 mb-3">Tambah Jurusan</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.jurusan.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Kode Jurusan</label>
                <input type="text" class="form-control" name="code" placeholder="Contoh: RPL" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Jurusan</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
