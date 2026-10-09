@extends('layouts.guru')

@section('title', 'Tambah Event Ujian')

@section('content')
<div class="mb-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.exam-event.index') }}">Manage event ujian</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card shadow-sm border-0 border-start border-primary border-4">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                <i class="align-middle me-2 text-primary" data-feather="plus-circle"></i>
                <h6 class="card-title mb-0 fw-bold">Tambah Event Ujian</h6>
            </div>
            <div class="card-body pt-0">
                <form action="{{ route('guru.exam-event.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nama Event</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: STS SEMESTER GENAP 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Keterangan</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Opsional..."></textarea>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary" style="background-color: #3b31c1; border: none;">
                            Simpan Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .breadcrumb-item a { color: #3b31c1; text-decoration: none; }
</style>
@endsection
