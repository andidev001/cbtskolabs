@extends('layouts.admin')

@section('title', 'Edit Guru')

@section('content')
<div class="d-flex align-items-center mb-3 gap-2">
    <a href="{{ route('admin.guru.index') }}" class="btn btn-sm btn-outline-secondary">
        <i data-feather="arrow-left"></i>
    </a>
    <h1 class="h3 mb-0">Edit Data Guru</h1>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Foto preview --}}
                @if($guru->photo)
                <div class="col-12">
                    <img src="{{ asset('storage/' . $guru->photo) }}" alt="Foto"
                         class="rounded-circle" style="width:80px;height:80px;object-fit:cover;">
                </div>
                @endif

                {{-- Nama --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $guru->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- NIP --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">NIP</label>
                    <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                           value="{{ old('nip', $guru->nip) }}">
                    @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Username --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                           value="{{ old('username', $guru->username) }}" required>
                    @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Password --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Kosongkan jika tidak diubah">
                        <button class="btn btn-outline-secondary" type="button"
                                onclick="togglePass('password', this)">
                            <i data-feather="eye"></i>
                        </button>
                    </div>
                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $guru->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Phone --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">No. HP</label>
                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $guru->phone) }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Foto --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ganti Foto</label>
                    <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror"
                           accept="image/*">
                    @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Status --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                               value="1" {{ old('is_active', $guru->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="col-12 d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">
                        <i data-feather="save" class="me-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.guru.subjects', $guru->id) }}" class="btn btn-info text-white">
                        <i data-feather="book-open" class="me-1"></i> Kelola Mata Pelajaran
                    </a>
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function togglePass(id, btn) {
    const inp = document.getElementById(id);
    inp.type = inp.type === 'password' ? 'text' : 'password';
    btn.innerHTML = inp.type === 'password' ? feather.icons['eye'].toSvg() : feather.icons['eye-off'].toSvg();
}
</script>
@endpush
@endsection
