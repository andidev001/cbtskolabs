@extends('layouts.admin')

@section('title', 'Edit Agama')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.religion.index') }}" class="btn btn-secondary btn-sm"><i class="align-middle" data-feather="arrow-left"></i> Kembali</a>
</div>
<h1 class="h3 mb-3">Edit Agama</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.religion.update', $religion->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nama Agama</label>
                <input type="text" class="form-control" name="name" value="{{ $religion->name }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
