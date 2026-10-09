@extends('layouts.admin')

@section('title', 'Detail Soal')

@section('content')
<div class="mb-3">
    <h1 class="h3 d-inline align-middle">Detail Soal</h1>
    <a href="{{ route('admin.bank-soal.show', $question->bank_soal_id) }}" class="btn btn-outline-secondary float-end">
        <i class="align-middle" data-feather="arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Butir Soal - {{ $question->type }}</h5>
    </div>
    <div class="card-body">
        <div class="question-preview border p-4 rounded mb-4 bg-light">
            <div class="mb-3 fw-bold">Pertanyaan:</div>
            <div class="mb-4">
                {!! $question->question_text !!}
            </div>

            @if(in_array($question->type, ['PG', 'LS', 'PGK']))
                <div class="options">
                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                        @php $field = 'option_' . $opt; @endphp
                        @if($question->$field)
                            @php
                                $isKunci = false;
                                if ($question->type == 'PGK' && $question->answer) {
                                    $kunciArr = json_decode($question->answer, true) ?: [];
                                    $isKunci = in_array($opt, $kunciArr);
                                } else {
                                    $isKunci = ($question->answer == $opt);
                                }
                            @endphp
                            <div class="d-flex mb-2 p-2 rounded {{ $isKunci ? 'bg-success text-white' : 'bg-white border' }}">
                                <div class="me-2 fw-bold text-uppercase">{{ $opt }}.</div>
                                <div>{!! $question->$field !!}</div>
                                @if($isKunci)
                                    <span class="ms-auto"><i data-feather="check-circle"></i> Kunci</span>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            @elseif($question->type == 'MJ')
                <div class="mt-4 p-3 bg-white border">
                    <h6 class="fw-bold">Pasangan Menjodohkan:</h6>
                    @php $mj = json_decode($question->complex_options, true); @endphp
                    @if($mj && isset($mj['left']))
                        <ul>
                            @foreach($mj['left'] as $idx => $leftItem)
                                <li><strong>{{ $leftItem }}</strong> &mdash; <em>{{ $mj['right'][$idx] ?? '' }}</em></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @elseif($question->type == 'MT')
                <div class="mt-4 p-3 bg-white border">
                    <h6 class="fw-bold">Urutan yang Benar:</h6>
                    @php $mt = json_decode($question->complex_options, true); @endphp
                    @if($mt && isset($mt['items']))
                        <ol>
                            @foreach($mt['items'] as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            @else
                <div class="mt-4 p-3 border-start border-4 border-info bg-white">
                    <strong>Kunci / Referensi:</strong> {{ $question->answer ?: '-' }}
                </div>
            @endif
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="150">Bank Soal</th>
                        <td>: {{ $question->bankSoal->name }}</td>
                    </tr>
                    <tr>
                        <th>Mata Pelajaran</th>
                        <td>: {{ $question->bankSoal->subject->name }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('admin.question.edit', $question->id) }}" class="btn btn-primary">
                    <i class="align-middle" data-feather="edit-2"></i> Edit Soal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
