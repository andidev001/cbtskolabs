@extends('layouts.admin')

@section('title', 'Edit Mata Pelajaran')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.subject.index') }}" class="btn btn-secondary btn-sm"><i class="align-middle" data-feather="arrow-left"></i> Kembali</a>
</div>
<h1 class="h3 mb-3">Edit Mata Pelajaran</h1>
<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.subject.update', $subject->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Kode Mapel</label>
                <input type="text" class="form-control" name="code" value="{{ $subject->code }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama Mata Pelajaran</label>
                <input type="text" class="form-control" name="name" value="{{ $subject->name }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
@endsection
