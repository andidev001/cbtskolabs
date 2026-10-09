<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Ujian | SKOLABS CBT</title>
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f4f7f9 !important; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 30px 16px; }
        .result-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 40px;
            max-width: 460px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .score-ring {
            width: 140px; height: 140px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column;
            margin: 0 auto 24px;
            position: relative;
        }
        .score-ring::before {
            content: '';
            position: absolute; inset: 0;
            border-radius: 50%;
            background: conic-gradient(
                var(--score-color) calc(var(--pct) * 1%), #e2e8f0 0%
            );
        }
        .score-ring::after {
            content: '';
            position: absolute; inset: 16px;
            border-radius: 50%;
            background: #ffffff; 
        }
        .score-inner { position: relative; z-index: 1; }
        .score-num { font-size: 2.2rem; font-weight: 800; line-height: 1; }
        .score-label { font-size: .7rem; color: #64748b; margin-top: 2px; }
        h2 { color: #1e293b; font-weight: 700; }
        .stat-row { display: flex; gap: 12px; margin: 20px 0; }
        .stat-item {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 10px;
        }
        .stat-item .num { font-size: 1.4rem; font-weight: 700; }
        .stat-item .lbl { font-size: .7rem; color: #64748b; margin-top: 2px; }
        .btn-back {
            display: inline-block;
            background: #1a4da1;
            color: #fff; border: none; border-radius: 10px;
            padding: 12px 32px; font-weight: 700; font-size: .9rem;
            text-decoration: none; transition: all .2s; margin-top: 8px;
            box-shadow: 0 4px 12px rgba(26, 77, 161, 0.2);
        }
        .btn-back:hover { color: #fff; opacity: .9; transform: translateY(-1px); box-shadow: 0 6px 15px rgba(26, 77, 161, 0.3); }
        p.sub { color: #64748b; font-size: .85rem; }
    </style>
</head>
<body>
<div class="result-card">
    @php
        $pct = $score;
        $color = $score >= 70 ? '#34d399' : ($score >= 50 ? '#f59e0b' : '#ef4444');
        $label = $score >= 70 ? '🎉 Lulus!' : ($score >= 50 ? '😐 Cukup' : '😞 Perlu Belajar Lagi');
    @endphp

    @if($schedule->show_score)
    <div class="score-ring" style="--pct: {{ $pct }}; --score-color: {{ $color }};">
        <div class="score-inner">
            <div class="score-num" style="color: {{ $color }};">
                {{ $score }}
            </div>
            <div class="score-label">NILAI</div>
        </div>
    </div>
    <h2>{{ $label }}</h2>
    @else
    <div class="mb-4">
        <div class="display-1 mb-3" style="color: #60a5fa;">
            <i data-feather="check-circle" style="width: 80px; height: 80px;"></i>
        </div>
        <h2 class="mt-3">Ujian Selesai!</h2>
        <p class="text-muted" style="color: #64748b !important;">Jawaban Anda telah berhasil dikirim dan tersimpan di sistem.</p>
    </div>
    @endif

    <p class="sub">Ujian <strong style="color:#1a4da1;">{{ $schedule->name }}</strong> telah selesai.</p>

    @if($schedule->show_score)
    <div class="stat-row">
        <div class="stat-item">
            <div class="num" style="color:#34d399;">{{ $correct }}</div>
            <div class="lbl">Benar</div>
        </div>
        <div class="stat-item">
            <div class="num" style="color:#ef4444;">{{ $total - $correct }}</div>
            <div class="lbl">Salah</div>
        </div>
        <div class="stat-item">
            <div class="num" style="color:#9ca3af;">{{ $total }}</div>
            <div class="lbl">Total Soal</div>
        </div>
    </div>
    @endif

    <p class="sub mb-3" style="font-size:.75rem;">
        Terima kasih telah mengerjakan ujian ini, {{ $siswa->name }}.<br>
        Hasil sudah tercatat oleh sistem.
    </p>

    <a href="{{ route('siswa.dashboard') }}" class="btn-back">← Kembali ke Dashboard</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>
    feather.replace();
</script>
</body>
</html>

