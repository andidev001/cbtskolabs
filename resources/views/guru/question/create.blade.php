@extends('layouts.guru')

@section('title', 'Tambah Soal')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('guru.bank-soal.show', $bankSoal->id) }}" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
            <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i>
        </a>
        <div>
            <h1 class="h3 mb-1 fw-bold" style="letter-spacing: -0.5px;">Tambah Soal {{ strtoupper($type) }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small" style="background: transparent; padding: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guru.bank-soal.index') }}" class="text-decoration-none">Bank Soal</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('guru.bank-soal.show', $bankSoal->id) }}" class="text-decoration-none">{{ $bankSoal->code }}</a></li>
                    <li class="breadcrumb-item active">Tambah Soal</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<form action="{{ route('guru.question.store', $bankSoal->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="type" value="{{ $type }}">
    <input type="hidden" name="bank_soal_id" value="{{ $bankSoal->id }}">

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="card-title mb-0 fw-bold">Pertanyaan</h6>
                </div>
                <div class="card-body pt-2">
                    <textarea name="question_text" id="question_text" class="form-control editor border-light-subtle @error('question_text') is-invalid @enderror" rows="4" required>{{ old('question_text') }}</textarea>
                    @error('question_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            @if(in_array($type, ['pg', 'pgk', 'ls']))
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold">Pilihan Jawaban & Kunci</h6>
                </div>
                <div class="card-body pt-3" id="options-container">
                    <p class="text-muted small mb-3">Tandai bulatan/kotak di sebelah kiri pilihan yang merupakan jawaban benar.</p>
                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        <div class="d-flex align-items-start mb-4 gap-3 option-row" id="row_{{ $opt }}">
                            <div class="d-flex flex-column align-items-center pt-2">
                                <div class="form-check p-0 m-0">
                                    @if($type === 'pgk')
                                    <input class="form-check-input custom-checkbox" type="checkbox" name="answer[]" value="{{ $opt }}" id="answer_{{ $opt }}">
                                    @else
                                    <input class="form-check-input custom-radio" type="radio" name="answer" value="{{ $opt }}" id="answer_{{ $opt }}" required>
                                    @endif
                                </div>
                                <label class="fw-bold mt-1 text-muted small" for="answer_{{ $opt }}">{{ strtoupper($opt) }}</label>
                            </div>
                            <div class="flex-grow-1">
                                <textarea name="option_{{ $opt }}" id="option_{{ $opt }}" class="form-control editor" rows="2">{{ old('option_'.$opt) }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            @elseif($type === 'mj')
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="card-title mb-0 fw-bold">Pasangan Menjodohkan</h6>
                </div>
                <div class="card-body pt-3">
                    <p class="text-muted small">Tambahkan baris untuk setiap pasangan yang benar. Kiri (Premis), Kanan (Respon).</p>
                    <div class="row g-2 mb-2 fw-bold text-center text-muted" style="font-size: 0.85rem;"><div class="col-5">Pernyataan / Soal (Kiri)</div><div class="col-6">Jawaban / Pasangan (Kanan)</div><div class="col-1"></div></div><div id="mj-container">
                        <div class="row g-2 mb-2 mj-row">
                            <div class="col-5"><input type="text" name="complex_options_array[left][]" class="form-control" placeholder="Tulis soal/pernyataan di sini..." required></div>
                            <div class="col-6"><input type="text" name="complex_options_array[right][]" class="form-control" placeholder="Tulis kunci jawaban pasangan di sini..." required></div>
                            <div class="col-1"><button type="button" class="btn btn-outline-danger w-100 remove-mj"><i data-feather="x"></i></button></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addMjRow()">+ Tambah Pasangan</button>
                </div>
            </div>

            @elseif($type === 'mt')
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="card-title mb-0 fw-bold">Urutan (Dari Atas ke Bawah)</h6>
                </div>
                <div class="card-body pt-3">
                    <p class="text-muted small">Masukkan item dalam urutan yang <b>benar</b>.</p>
                    <div id="mt-container">
                        <div class="input-group mb-2 mt-row">
                            <span class="input-group-text">1</span>
                            <input type="text" name="complex_options_array[items][]" class="form-control" placeholder="Item urutan..." required>
                            <button type="button" class="btn btn-outline-danger remove-mt"><i data-feather="x"></i></button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addMtRow()">+ Tambah Item</button>
                </div>
            </div>

            @elseif(in_array($type, ['bs', 'st']))
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Pilih Jawaban Benar:</h6>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="answer" value="a" id="ans_a" required>
                        <label class="form-check-label" for="ans_a">{{ $type == 'bs' ? 'Benar' : 'Setuju' }}</label>
                        <input type="hidden" name="option_a" value="{{ $type == 'bs' ? 'Benar' : 'Setuju' }}">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer" value="b" id="ans_b" required>
                        <label class="form-check-label" for="ans_b">{{ $type == 'bs' ? 'Salah' : 'Tidak Setuju' }}</label>
                        <input type="hidden" name="option_b" value="{{ $type == 'bs' ? 'Salah' : 'Tidak Setuju' }}">
                    </div>
                </div>
            </div>

            @elseif($type === 'is')
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Kunci Jawaban Isian Singkat</h6>
                    <input type="text" name="answer" class="form-control" placeholder="Ketik jawaban benar di sini..." required>
                </div>
            </div>
            
            @elseif($type === 'ur')
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-2">Referensi Jawaban Essay (Opsional)</h6>
                    <textarea name="answer" class="form-control" rows="3" placeholder="Panduan jawaban..."></textarea>
                </div>
            </div>
            @endif

        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background-color: #f8f9fa;">
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Tipe Soal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white text-muted"><i data-feather="layers" style="width:16px;"></i></span>
                            <input type="text" class="form-control bg-white" value="{{ strtoupper($type) }}" readonly>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">Level Kognitif</label>
                        <select name="cognitive_level" class="form-select">
                            <option value="">-- Pilih Level Kognitif --</option>
                            <option value="C1">C1 (Mengingat)</option>
                            <option value="C2">C2 (Memahami)</option>
                            <option value="C3">C3 (Mengaplikasikan)</option>
                            <option value="C4">C4 (Menganalisis)</option>
                            <option value="C5">C5 (Mengevaluasi)</option>
                            <option value="C6">C6 (Mencipta)</option>
                        </select>
                    </div>

                    @if(in_array($type, ['ls']))
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">File Audio (.mp3, .wav)</label>
                        <input type="file" name="audio_file" class="form-control" accept="audio/*">
                    </div>
                    @endif

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary py-2 fw-bold" style="background-color: #3128a1;">
                            <i data-feather="save" class="me-1"></i> Simpan Soal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('question_text')) initCKEditor('question_text');
        ['a','b','c','d','e'].forEach(opt => {
            if(document.getElementById('option_'+opt)) initCKEditor('option_'+opt);
        });
        feather.replace();
    });

    function initCKEditor(id) {
        CKEDITOR.replace(id, {
            versionCheck: false,
            height: id === 'question_text' ? 150 : 80,
            filebrowserImageUploadUrl: "{{ route('guru.question.upload-image', ['_token' => csrf_token()]) }}",
            filebrowserUploadMethod: 'form',
            extraPlugins: 'uploadimage,clipboard',
            toolbar: [
                { name: 'document', items: [ 'Source' ] },
                { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
                '/',
                { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar' ] },
                '/',
                { name: 'styles', items: [ 'Styles', 'Format', 'Font', 'FontSize' ] },
                { name: 'colors', items: [ 'TextColor', 'BGColor' ] }
            ]
        });
    }

    function addMjRow() {
        const container = document.getElementById('mj-container');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 mj-row';
        row.innerHTML = `<div class="col-5"><input type="text" name="complex_options_array[left][]" class="form-control" placeholder="Tulis soal/pernyataan di sini..." required></div><div class="col-6"><input type="text" name="complex_options_array[right][]" class="form-control" placeholder="Tulis kunci jawaban pasangan di sini..." required></div><div class="col-1"><button type="button" class="btn btn-outline-danger w-100 remove-mj" onclick="this.parentElement.parentElement.remove()"><i data-feather="x"></i></button></div>`;
        container.appendChild(row);
        feather.replace();
    }
    
    function addMtRow() {
        const container = document.getElementById('mt-container');
        const count = container.children.length + 1;
        const row = document.createElement('div');
        row.className = 'input-group mb-2 mt-row';
        row.innerHTML = `<span class="input-group-text">${count}</span><input type="text" name="complex_options_array[items][]" class="form-control" placeholder="Item urutan..." required><button type="button" class="btn btn-outline-danger remove-mt" onclick="this.parentElement.remove(); updateMtNumbers();"><i data-feather="x"></i></button>`;
        container.appendChild(row);
        feather.replace();
    }
    
    function updateMtNumbers() {
        const rows = document.querySelectorAll('.mt-row .input-group-text');
        rows.forEach((el, idx) => el.innerText = idx + 1);
    }
    
    document.addEventListener('click', function(e) {
        if(e.target.closest('.remove-mj')) e.target.closest('.mj-row').remove();
    });
</script>
@endsection


