<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $schedule->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.5; color: #000; background: #fff; margin: 0; padding: 0; }
        table.kop-surat { width: 100%; border-bottom: 3px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        table.kop-surat td.logo-cell { width: 100px; text-align: center; vertical-align: middle; }
        table.kop-surat td.text-cell { text-align: center; vertical-align: middle; }
        .logo { width: 80px; height: auto; }
        .kop-text h2, .kop-text h3, .kop-text p { margin: 0; }
        .kop-text h2 { font-size: 16pt; text-transform: uppercase; }
        .kop-text h3 { font-size: 14pt; }
        .title { text-align: center; font-size: 14pt; font-weight: bold; text-decoration: underline; margin-bottom: 20px; text-transform: uppercase; }
        
        table.info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 2px; vertical-align: top; }
        .info-table td:first-child { width: 20%; }
        .info-table td:nth-child(2) { width: 2%; }
        .info-table td:nth-child(3) { width: 35%; font-weight: bold; }
        .info-table td:nth-child(4) { width: 15%; }
        .info-table td:nth-child(5) { width: 2%; }
        .info-table td:nth-child(6) { width: 26%; font-weight: bold; }

        table.students-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .students-table th, .students-table td { border: 1px solid #000; padding: 5px; vertical-align: middle; }
        .students-table th { background-color: #f2f2f2; text-align: center; font-weight: bold; }
        
        .signature-cell { width: 120px; text-align: left; vertical-align: top; height: 35px; }
        .signature-number { font-size: 9pt; }
        
        table.footer-table { width: 100%; margin-top: 30px; text-align: center; page-break-inside: avoid; }
        table.footer-table td { width: 50%; vertical-align: top; }
        .signature-space { height: 80px; }
        .text-bold { font-weight: bold; }
    </style>
</head>
<body>
    @php
        // Prepare Logo
        $logo_setting = \App\Models\Setting::get('logo');
        $favicon = $logo_setting ? public_path($logo_setting) : public_path('logoskolabs.png');
        if(!file_exists($favicon)) {
            $favicon = '';
        } else {
            $type = pathinfo($favicon, PATHINFO_EXTENSION);
            $data = file_get_contents($favicon);
            $favicon = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        // Prepare Kop Surat
        $kop_setting = \App\Models\Setting::get('kop_surat');
        $kop_surat = $kop_setting ? public_path($kop_setting) : '';
        if(file_exists($kop_surat) && is_file($kop_surat)) {
            $type = pathinfo($kop_surat, PATHINFO_EXTENSION);
            $data = file_get_contents($kop_surat);
            $kop_surat = 'data:image/' . $type . ';base64,' . base64_encode($data);
        } else {
            $kop_surat = '';
        }

        $app_name = \App\Models\Setting::get('nama_instansi', \App\Models\Setting::get('app_name', 'SKOLABS CBT'));
        $app_address = \App\Models\Setting::get('alamat', '______________________________________________________________');
        
        $ruang = $ba ? $ba->ruang : '-';
        $sesi = $ba ? $ba->sesi : '-';
        $proktor = $ba ? $ba->proktor : '_____________________';
        $pengawas = $ba ? $ba->pengawas : '_____________________';
    @endphp

    @foreach($groups as $indexGroup => $group)
        @php
            $students = $studentsByGroup->get($group->id, collect());
        @endphp
        
        <div style="{{ $indexGroup > 0 ? 'page-break-before: always;' : '' }}">
            @if($kop_surat)
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="{{ $kop_surat }}" style="max-width: 100%; height: auto; max-height: 150px;" alt="Kop Surat">
                </div>
            @else
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
                                <p>Alamat: {{ $app_address }}</p>
                            </div>
                        </td>
                    </tr>
                </table>
            @endif

            <div class="title">DAFTAR HADIR PESERTA UJIAN</div>
            
            <table class="info-table">
                <tr>
                    <td>Mata Pelajaran</td>
                    <td>:</td>
                    <td>{{ $schedule->bankSoal->subject->name ?? $schedule->bankSoal->name }}</td>
                    <td>Hari/Tanggal</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($schedule->start_at)->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <td>Kelas / Grup</td>
                    <td>:</td>
                    <td>{{ $group->name }}</td>
                    <td>Waktu</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($schedule->start_at)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_at)->format('H:i') }}</td>
                </tr>
                <tr>
                    <td>Ruang / Sesi</td>
                    <td>:</td>
                    <td>{{ $ruang }} / {{ $sesi }}</td>
                    <td>Jumlah Peserta</td>
                    <td>:</td>
                    <td>{{ $students->count() }} orang</td>
                </tr>
            </table>
            
            <table class="students-table">
                <thead>
                    <tr>
                        <th width="30">No</th>
                        <th width="120">No Peserta</th>
                        <th>Nama Peserta</th>
                        <th width="80">Kelas</th>
                        <th colspan="2" width="160">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td style="text-align: center;">{{ $student->nomer_ujian ?: $student->nisn ?: '-' }}</td>
                            <td>{{ $student->name }}</td>
                            <td style="text-align: center;">{{ $student->group->name ?? '-' }}</td>
                            @if(($index + 1) % 2 != 0)
                                <td class="signature-cell"><span class="signature-number">{{ $index + 1 }}.</span></td>
                                <td class="signature-cell"></td>
                            @else
                                <td class="signature-cell"></td>
                                <td class="signature-cell"><span class="signature-number">{{ $index + 1 }}.</span></td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center;">Tidak ada data peserta untuk ujian ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            <table class="footer-table">
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
        </div>
    @endforeach
</body>
</html>

