@extends('layouts.admin')

@section('title', 'Edit Grup')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.group.index') }}" class="btn btn-secondary btn-sm">
        <i class="align-middle" data-feather="arrow-left"></i> Kembali
    </a>
</div>

<h1 class="h3 mb-3">Edit Grup: {{ $group->name }}</h1>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.group.update', $group->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nama Grup / Kelas</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $group->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">Update Grup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
