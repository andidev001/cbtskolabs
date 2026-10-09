@extends('layouts.admin')

@section('title', 'Edit User Admin')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.user.index') }}" class="btn btn-secondary btn-sm"><i class="align-middle" data-feather="arrow-left"></i> Kembali</a>
</div>
<h1 class="h3 mb-3">Edit User Admin</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ganti)</small></label>
                <input type="password" class="form-control" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
