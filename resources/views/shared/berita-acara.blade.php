<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara - {{ $schedule->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; background: #fff; margin: 0; padding: 20px; }
        .kop-surat { border-bottom: 3px solid #000; padding-bottom: 10px; margin-bottom: 20px; display: flex; align-items: center; }
        .logo { width: 80px; height: auto; margin-right: 20px; }
        .kop-text { flex: 1; text-align: center; }
        .kop-text h2, .kop-text h3, .kop-text p { margin: 0; }
        .kop-text h2 { font-size: 16pt; text-transform: uppercase; }
        .kop-text h3 { font-size: 14pt; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table-info td { padding: 4px; vertical-align: top; }
        .table-info td:first-child { width: 30%; }
        .table-info td:nth-child(2) { width: 2%; }
        .signature-area { margin-top: 50px; width: 100%; }
        .signature-box { float: left; width: 33%; text-align: center; }
        .signature-box.right { float: right; }
        .signature-space { height: 80px; }
        .clear { clear: both; }
        @media print {
            body { padding: 0; }
            @page { size: A4; margin: 2cm; }
            .btn-print { display: none !important; }
        }
        .btn-print { padding: 10px 20px; background-color: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer; margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 14px; }
        .btn-print:hover { background-color: #0b5ed7; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    <button type="button" class="btn-print" onclick="window.print()">Cetak Berita Acara</button>
    <div class="kop-surat">
        @php
            $app_logo = \App\Models\Setting::get('app_logo');
            $favicon = $app_logo ? \Storage::url($app_logo) : asset('logoskolabs.png');
            $app_name = \App\Models\Setting::get('app_name', 'SKOLABS CBT');
        @endphp
        <img src="{{ $favicon }}" alt="Logo" class="logo">
        <div class="kop-text">
            <h2>PANITIA PELAKSANAAN UJIAN</h2>
            <h3>{{ strtoupper($app_name) }}</h3>
            <p>Alamat: ______________________________________________________________</p>
        </div>
    </div>
    <div class="title">BERITA ACARA PELAKSANAAN UJIAN</div>
    <p>Pada hari ini <strong>{{ \Carbon\Carbon::now()->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::now()->format('d') }}</strong> bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F') }}</strong> tahun <strong>{{ \Carbon\Carbon::now()->format('Y') }}</strong>, telah dilaksanakan ujian untuk mata pelajaran / bank soal <strong>{{ $schedule->bankSoal->name }}</strong> dari pukul <strong>{{ \Carbon\Carbon::parse($schedule->start_at)->format('H:i') }}</strong> sampai dengan <strong>{{ \Carbon\Carbon::parse($schedule->end_at)->format('H:i') }}</strong>.</p>
    <table class="table-info">
        <tr><td>Nama Ujian</td><td>:</td><td class="text-bold">{{ $schedule->name }}</td></tr>
        <tr><td>Event Ujian</td><td>:</td><td>{{ $schedule->examEvent->name ?? '-' }}</td></tr>
        <tr><td>Kelas/Grup</td><td>:</td><td>{{ $schedule->groups->pluck('name')->join(', ') }}</td></tr>
        <tr><td>Ruang / Sesi</td><td>:</td><td>{{ request('ruang', '_____') }} / {{ request('sesi', '_____') }}</td></tr>
        <tr><td>Jumlah Peserta Terdaftar</td><td>:</td><td>{{ $total_students }} orang</td></tr>
        <tr><td>Jumlah Peserta Hadir</td><td>:</td><td>{{ $total_present }} orang</td></tr>
        <tr><td>Jumlah Peserta Tidak Hadir</td><td>:</td><td>{{ $total_absent }} orang</td></tr>
        <tr><td>Daftar Peserta Tidak Hadir</td><td>:</td><td>
                @if(count($absent_students) > 0)
                    {{ implode(', ', $absent_students) }}
                @else
                    -
                @endif
            </td></tr>
        <tr><td>Catatan Selama Ujian</td><td>:</td><td>{{ request('catatan', 'Ujian berjalan dengan tertib dan lancar.') }}</td></tr>
    </table>
    <p>Demikian Berita Acara ini dibuat dengan sesungguhnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    <div class="signature-area">
        <div class="signature-box"><p>Proktor / Teknisi,</p><div class="signature-space"></div><p class="text-bold">{{ request('proktor', '_____________________') }}</p><p>NIP. </p></div>
        <div class="signature-box right"><p>Pengawas Ujian,</p><div class="signature-space"></div><p class="text-bold">{{ request('pengawas', '_____________________') }}</p><p>NIP. </p></div>
        <div class="clear"></div>
    </div>
</body>
</html>

