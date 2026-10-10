<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $schedule->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; color: #000; background: #fff; margin: 0; padding: 0; }
        table.kop-surat { width: 100%; border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        table.kop-surat td.logo-cell { width: 100px; text-align: center; vertical-align: middle; }
        table.kop-surat td.text-cell { text-align: center; vertical-align: middle; }
        .logo { width: 80px; height: auto; }
        .kop-text h2, .kop-text h3, .kop-text p { margin: 0; }
        .kop-text h2 { font-size: 16pt; text-transform: uppercase; }
        .kop-text h3 { font-size: 14pt; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase; }
        table.content-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .content-table td { padding: 4px; vertical-align: top; }
        .content-table td:first-child { width: 30%; }
        .content-table td:nth-child(2) { width: 2%; }
        table.signature-table { width: 100%; margin-top: 50px; text-align: center; }
        table.signature-table td { width: 50%; vertical-align: top; }
        .signature-space { height: 80px; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    @php
        $app_logo = \App\Models\Setting::get('app_logo');
        $favicon = $app_logo ? public_path('storage/' . $app_logo) : public_path('logoskolabs.png');
        if(!file_exists($favicon)) {
            $favicon = '';
        } else {
            $type = pathinfo($favicon, PATHINFO_EXTENSION);
            $data = file_get_contents($favicon);
            $favicon = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        $app_name = \App\Models\Setting::get('app_name', 'SKOLABS CBT');
        
        $ba = $schedule->beritaAcara;
        $ruang = $ba ? $ba->ruang : '_____';
        $sesi = $ba ? $ba->sesi : '_____';
        $proktor = $ba ? $ba->proktor : '_____________________';
        $pengawas = $ba ? $ba->pengawas : '_____________________';
        $catatan = $ba ? $ba->catatan : 'Ujian berjalan dengan tertib dan lancar.';
    @endphp

    <table class="kop-surat">
        <tr>
            <td class="logo-cell">
                @if($favicon)
                    <img src="{{ $favicon }}" alt="Logo" class="logo">
                @endif
            </td>
            <td class="text-cell">
                <div class="kop-text">
                    <h2>PANITIA PELAKSANAAN UJIAN</h2>
                    <h3>{{ strtoupper($app_name) }}</h3>
                    <p>Alamat: ______________________________________________________________</p>
                </div>
            </td>
        </tr>
    </table>

    <div class="title">BERITA ACARA PELAKSANAAN UJIAN</div>
    <p>Pada hari ini <strong>{{ \Carbon\Carbon::now()->translatedFormat('l') }}</strong> tanggal <strong>{{ \Carbon\Carbon::now()->format('d') }}</strong> bulan <strong>{{ \Carbon\Carbon::now()->translatedFormat('F') }}</strong> tahun <strong>{{ \Carbon\Carbon::now()->format('Y') }}</strong>, telah dilaksanakan ujian untuk mata pelajaran / bank soal <strong>{{ $schedule->bankSoal->name }}</strong> dari pukul <strong>{{ \Carbon\Carbon::parse($schedule->start_at)->format('H:i') }}</strong> sampai dengan <strong>{{ \Carbon\Carbon::parse($schedule->end_at)->format('H:i') }}</strong>.</p>
    
    <table class="content-table">
        <tr><td>Nama Ujian</td><td>:</td><td class="text-bold">{{ $schedule->name }}</td></tr>
        <tr><td>Event Ujian</td><td>:</td><td>{{ $schedule->examEvent->name ?? '-' }}</td></tr>
        <tr><td>Kelas/Grup</td><td>:</td><td>{{ $schedule->groups->pluck('name')->join(', ') }}</td></tr>
        <tr><td>Ruang / Sesi</td><td>:</td><td>{{ $ruang }} / {{ $sesi }}</td></tr>
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
        <tr><td>Catatan Selama Ujian</td><td>:</td><td>{{ $catatan }}</td></tr>
    </table>
    
    <p>Demikian Berita Acara ini dibuat dengan sesungguhnya untuk dapat dipergunakan sebagaimana mestinya.</p>
    
    <table class="signature-table">
        <tr>
            <td>
                <p>Proktor / Teknisi,</p>
                <div class="signature-space"></div>
                <p class="text-bold">{{ $proktor }}</p>
                <p>NIP. </p>
            </td>
            <td>
                <p>Pengawas Ujian,</p>
                <div class="signature-space"></div>
                <p class="text-bold">{{ $pengawas }}</p>
                <p>NIP. </p>
            </td>
        </tr>
    </table>
</body>
</html>

