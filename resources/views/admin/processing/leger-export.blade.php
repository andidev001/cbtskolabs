<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Leger Nilai - {{ $selectedGroup->name }}</title>
    @if($isPdf)
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; }
    </style>
    @endif
</head>
<body>
    <div class="text-center mb-4">
        <h2>Leger Nilai Peserta</h2>
        <p><strong>Kelas/Grup:</strong> {{ $selectedGroup->name }}<br>
        <strong>Event:</strong> {{ $selectedEvent->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30" rowspan="2" class="align-middle">No</th>
                <th rowspan="2" class="align-middle">Nama Siswa</th>
                <th colspan="{{ $schedules->count() ?: 1 }}" class="align-middle">Mata Ujian / Skor</th>
                <th rowspan="2" class="align-middle" width="50">Rata2</th>
            </tr>
            <tr>
                @forelse($schedules as $sch)
                    <th>{{ $sch->name }}</th>
                @empty
                    <th>Tidak ada jadwal</th>
                @endforelse
            </tr>
        </thead>
        <tbody>
            @forelse($siswa as $s)
                @php $totalScore = 0; $count = 0; @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="fw-bold">{{ $s->name }}</td>
                    @foreach($schedules as $sch)
                        @php 
                            $res = $results->get($s->id)?->where('exam_schedule_id', $sch->id)->first();
                            if($res) { $totalScore += $res->score; $count++; }
                        @endphp
                        <td class="text-center">
                            {{ $res ? $res->score : '-' }}
                        </td>
                    @endforeach
                    <td class="text-center fw-bold">
                        {{ $count > 0 ? round($totalScore / $count, 1) : '0' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $schedules->count() + 3 }}" class="text-center">Tidak ada data siswa di grup ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
