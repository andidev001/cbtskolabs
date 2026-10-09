@extends('layouts.admin')

@section('title', 'Tambah User Admin')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary btn-sm"><i class="align-middle" data-feather="arrow-left"></i> Kembali</a>
</div>
<h1 class="h3 mb-3">Tambah User Admin</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>
@endsection
