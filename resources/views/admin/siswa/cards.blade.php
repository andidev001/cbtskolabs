<!DOCTYPE html>
<html>
<head>
    <title>Kartu Peserta</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; margin: 0; padding: 0; }
        .card { 
            width: 48%; 
            border: 1px solid #000; 
            display: inline-block; 
            vertical-align: top;
            box-sizing: border-box;
            margin-bottom: 20px;
        }
        .card:nth-child(odd) {
            margin-right: 2%;
        }
        .header { 
            border-bottom: 1px solid #000;
            padding: 5px;
        }
        .header table { width: 100%; border-collapse: collapse; }
        .header td { text-align: center; vertical-align: middle; }
        .logo { width: 45px; height: auto; }
        .title { font-size: 11px; font-weight: bold; line-height: 1.3; }
        .qr-placeholder { width: 45px; height: 45px; border: 1px solid #000; text-align: center; font-size: 8px; line-height: 45px; margin-left: auto; }
        
        .body-table { width: 100%; border-collapse: collapse; margin-top: 5px; margin-bottom: 5px; }
        .body-table td { padding: 3px 5px; vertical-align: top; }
        .label { width: 110px; }
        .colon { width: 10px; text-align: center; }
        
        .footer-section { padding: 5px; width: 100%; border-spacing: 0; border-collapse: collapse; }
        .photo-box { 
            width: 75px; 
            height: 100px; 
            border: 1px solid #000; 
            background-color: #dc2626; 
            color: #fff; 
            text-align: center; 
            font-size: 10px;
            font-weight: bold;
        }
        .notes-box {
            border: 1px solid #000;
            vertical-align: top;
        }
        .notes-title {
            background-color: #e5e7eb;
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            padding: 3px 0;
        }
        .notes-list {
            padding: 5px 5px 5px 20px;
            margin: 0;
            font-size: 9px;
            line-height: 1.4;
        }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @foreach($siswas as $index => $siswa)
        <div class="card">
            <div class="header">
                <table>
                    <tr>
                        <td style="width: 55px; text-align: left;">
                            @php
                                $logoPath = isset($setting['logo']) && $setting['logo'] ? public_path($setting['logo']) : public_path('assets/adminkit/img/avatars/avatar.jpg');
                                $imgData = file_exists($logoPath) ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath)) : '';
                            @endphp
                            @if($imgData)
                                <img src="{{ $imgData }}" class="logo">
                            @endif
                        </td>
                        <td class="title">
                            KARTU LOGIN PESERTA<br>
                            {{ mb_strtoupper($title) }}
                        </td>
                        <td style="width: 55px; text-align: right;">
                            <div class="qr-placeholder">QR</div>
                        </td>
                    </tr>
                </table>
            </div>
            
            <table class="body-table">
                <tr>
                    <td class="label">Nama Peserta</td>
                    <td class="colon">:</td>
                    <td><strong>{{ mb_strtoupper($siswa->name) }}</strong></td>
                </tr>
                <tr>
                    <td class="label">NISN / No Ujian</td>
                    <td class="colon">:</td>
                    <td>{{ $siswa->nisn ?? $siswa->nomer_ujian }}</td>
                </tr>
                <tr>
                    <td class="label">Jurusan</td>
                    <td class="colon">:</td>
                    <td>{{ $siswa->jurusan ? $siswa->jurusan->name : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Ruang / Kelas</td>
                    <td class="colon">:</td>
                    <td>{{ $siswa->group ? $siswa->group->name : '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Username</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $siswa->username }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Password</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $siswa->password_raw ?? '******' }}</strong></td>
                </tr>
            </table>

            <table class="footer-section">
                <tr>
                    <td style="width: 80px; padding-right: 5px; padding-left: 5px;">
                        <div class="photo-box" style="background-color: transparent; color: #000;"><br><br><br><br>FOTO 3x4</div>
                    </td>
                    <td style="padding-right: 5px; padding-bottom: 5px;">
                        <table class="notes-box" style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td class="notes-title">CATATAN PENTING</td>
                            </tr>
                            <tr>
                                <td>
                                    <ul class="notes-list">
                                        <li>Kartu ini wajib dibawa saat pelaksanaan ujian berlangsung.</li>
                                        <li>Jaga kerahasiaan Username dan Password Anda.</li>
                                        <li>Login hanya dapat dilakukan pada ruang dan gelombang yang telah ditentukan.</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
        
        @if(($index + 1) % 8 == 0)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
