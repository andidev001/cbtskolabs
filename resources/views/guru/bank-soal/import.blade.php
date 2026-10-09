@extends('layouts.guru')

@section('title', 'Upload Soal Bank Soal')

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-transparent p-0">
            <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('guru.bank-soal.show', $bankSoal->id) }}">Kelola Soal</a></li>
            <li class="breadcrumb-item active">Upload soal banksoal</li>
        </ol>
    </nav>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        <div class="mb-4">
            <a href="{{ route('guru.bank-soal.show', $bankSoal->id) }}" class="btn btn-light border px-4">Kembali</a>
        </div>

        <div class="table-responsive mb-4">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Tipe</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Docx</td>
                        <td>
                            <a href="{{ route('guru.bank-soal.download-template-word') }}" class="text-primary text-decoration-none">
                                <i class="align-middle me-1" data-feather="download"></i> Download Docx format 1
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Docx | HTML Zip</td>
                        <td>
                            <a href="{{ route('guru.bank-soal.download-template-word') }}" class="text-primary text-decoration-none">
                                <i class="align-middle me-1" data-feather="download"></i> Download Docx format 2
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <form action="{{ route('guru.bank-soal.process-import-word', $bankSoal->id) }}" method="POST" enctype="multipart/form-data" class="p-4 border border-dashed rounded bg-light">
            @csrf
            <div class="mb-4">
                <label class="form-label fw-bold">Pilih Format</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i data-feather="file-text" style="width: 18px;"></i></span>
                    <select name="format" class="form-select">
                        <option value="format1">[Docx] Format 1</option>
                        <option value="format2">[Docx] Format 2</option>
                    </select>
                </div>
            </div>

            <div class="mb-0">
                <label class="form-label fw-bold">Pilih File</label>
                <div class="input-group">
                    <input type="file" name="file" class="form-control" placeholder="Pilih file docx, .zip...">
                    <button type="submit" class="btn btn-primary px-4" style="background-color: #3b31c1;">Upload</button>
                </div>
                <div class="mt-1 small text-muted">0%</div>
            </div>
        </form>
    </div>
</div>

<div class="alert alert-warning border-0 shadow-sm" style="background-color: #fff8e1; color: #856404;">
    <h6 class="fw-bold mb-2">Perhatian</h6>
    <p class="mb-0 small">Pastikan file yang di import sesuai dengan format. Jika import menggunakan HTML, lakukan zip terlebih dahulu antara file html dan folder .fld</p>
</div>

<style>
    .border-dashed { border-style: dashed !important; border-width: 2px !important; border-color: #dee2e6 !important; }
    .breadcrumb-item + .breadcrumb-item::before { content: "/"; }
</style>
@endsection

