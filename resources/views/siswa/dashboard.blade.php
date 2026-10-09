<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard Ujian | SKOLABS CBT</title>
    <link rel="shortcut icon" href="{{ asset('assets/adminkit/img/icons/icon-48x48.png') }}" />
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #f4f7fa; 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            color: #334155;
            min-height: 100vh;
        }

        /* ── Header Decor ── */
        .page-header {
            background: linear-gradient(135deg, #1a4da1 0%, #0d3a85 100%);
            color: #fff;
            padding: 30px 0 90px;
            position: relative;
            overflow: hidden;
        }
        .header-bg-shape {
            position: absolute;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        .shape-1 { width: 300px; height: 300px; top: -100px; left: -50px; }
        .shape-2 { width: 200px; height: 200px; bottom: -50px; right: 10%; }

        /* ── Navbar ── */
        .navbar-custom {
            padding: 12px 0;
            z-index: 10;
        }
        .brand-logo {
            font-size: 1.4rem;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            letter-spacing: -0.5px;
        }
        .brand-logo span { font-weight: 300; opacity: 0.9; }

        .user-nav-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 16px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            background: #fff;
            color: #1a4da1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }
        .logout-link {
            color: #fff;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            opacity: 0.8;
            transition: opacity 0.2s;
        }
        .logout-link:hover { opacity: 1; color: #fff; }

        /* ── Stats ── */
        .stats-container {
            margin-top: -60px;
            position: relative;
            z-index: 5;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.02);
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-info h3 { margin: 0; font-size: 1.4rem; font-weight: 700; color: #1e293b; }
        .stat-info p { margin: 0; font-size: 0.82rem; color: #64748b; font-weight: 500; }
        
        .icon-ongoing { background: #ecfdf5; color: #10b981; }
        .icon-upcoming { background: #eff6ff; color: #3b82f6; }
        .icon-finished { background: #f8fafc; color: #94a3b8; }

        /* ── Section Titles ── */
        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 40px 0 20px;
        }
        .section-header h2 { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; }
        .section-header .line { flex: 1; height: 1px; background: #e2e8f0; }

        /* ── Exam Cards ── */
        .exam-card {
            background: #fff;
            border-radius: 20px;
            padding: 0;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            border: 1px solid #eef2f6;
            overflow: hidden;
            display: flex;
            transition: all 0.3s;
        }
        .exam-card:hover { box-shadow: 0 15px 30px rgba(0,0,0,0.08); transform: translateY(-3px); }
        
        .card-status-indicator {
            width: 8px;
            background: #e2e8f0;
        }
        .ongoing .card-status-indicator { background: #10b981; }
        .upcoming .card-status-indicator { background: #3b82f6; }
        .finished .card-status-indicator { background: #cbd5e1; }

        .card-body-content {
            padding: 20px 22px;
            flex: 1;
            min-width: 0;
        }
        .exam-tag {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 4px 12px;
            border-radius: 50px;
            margin-bottom: 12px;
            display: inline-block;
        }
        .tag-ongoing { background: #d1fae5; color: #065f46; }
        .tag-upcoming { background: #dbeafe; color: #1e40af; }
        .tag-finished { background: #f1f5f9; color: #475569; }

        .exam-title { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 6px; line-height: 1.4; }
        .exam-event { font-size: 0.88rem; color: #64748b; margin-bottom: 14px; display: flex; align-items: center; gap: 6px; }

        .exam-details {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            padding-top: 14px;
            border-top: 1px solid #f1f5f9;
        }
        .detail-item { display: flex; align-items: center; gap: 6px; font-size: 0.82rem; color: #64748b; }
        .detail-item svg { width: 15px; height: 15px; color: #94a3b8; flex-shrink: 0; }

        /* ── Action Box ── */
        .card-action-box {
            padding: 20px 22px;
            background: #fafbfc;
            border-left: 1px solid #f1f5f9;
            width: 220px;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .btm-mulai {
            background: #1a4da1;
            color: #fff;
            border: none;
            padding: 11px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
            text-decoration: none;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
        }
        .btm-mulai:hover { background: #0d3a85; color: #fff; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(26,77,161,0.3); }

        .token-input {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 0.9rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 10px;
            width: 100%;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        .token-input:focus { border-color: #1a4da1; }

        .btm-disabled {
            background: #e2e8f0;
            color: #94a3b8;
            padding: 11px 20px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            cursor: not-allowed;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .countdown-timer {
            font-size: 0.82rem;
            color: #f59e0b;
            font-weight: 700;
            margin-top: 10px;
        }

        /* Tablet */
        @media (max-width: 992px) {
            .exam-card { flex-direction: column; }
            .card-action-box { width: 100%; border-left: none; border-top: 1px solid #f1f5f9; padding: 18px 22px; }
            .card-status-indicator { width: 100%; height: 6px; }
            .stats-container { margin-top: -50px; }
        }

        /* Mobile */
        @media (max-width: 576px) {
            /* Fix container padding so text doesn't touch edges */
            .container { padding-left: 20px !important; padding-right: 20px !important; }

            .page-header { padding: 18px 0 80px; }
            .navbar-custom { padding: 8px 0; }
            .brand-logo { font-size: 1.15rem; }
            .user-nav-card { padding: 5px 14px; gap: 10px; }
            .user-avatar { width: 30px; height: 30px; font-size: 0.78rem; }
            .logout-link { font-size: 0.82rem; }

            /* Hero text – give it breathing room */
            .hero-title { font-size: 1.6rem !important; letter-spacing: -0.5px !important; margin-bottom: 8px !important; line-height: 1.25 !important; }
            .hero-subtitle { font-size: 0.88rem !important; line-height: 1.5 !important; }
            .hero-badge { font-size: 0.77rem !important; padding: 5px 12px !important; }

            /* Stats – vertical layout on mobile */
            .stats-container { margin-top: -45px; }
            .stat-card {
                padding: 12px 8px;
                gap: 6px;
                border-radius: 14px;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .stat-icon { width: 36px; height: 36px; border-radius: 10px; flex-shrink: 0; }
            .stat-icon svg { width: 18px; height: 18px; }
            .stat-info { width: 100%; overflow: hidden; }
            .stat-info h3 { font-size: 1.15rem; }
            .stat-info p { font-size: 0.68rem; white-space: normal; word-break: break-word; line-height: 1.2; }

            /* Section header */
            .section-header { margin: 24px 0 12px; }
            .section-header h2 { font-size: 0.88rem; }

            /* Exam cards */
            .exam-card { border-radius: 16px; margin-bottom: 16px; }
            .card-body-content { padding: 15px 16px; }
            .exam-title { font-size: 0.97rem; }
            .exam-event { font-size: 0.8rem; margin-bottom: 10px; }
            .exam-details { gap: 10px; padding-top: 10px; }
            .detail-item { font-size: 0.77rem; }
            .card-action-box { padding: 14px 16px; }
            .btm-mulai { padding: 10px 16px; font-size: 0.88rem; border-radius: 10px; }
            .btm-disabled { padding: 10px 16px; font-size: 0.88rem; border-radius: 10px; }
            .token-input { font-size: 0.85rem; padding: 7px 10px; }
            .countdown-timer { font-size: 0.78rem; }
        }
    </style>
</head>
<body>

<div class="page-header">
    <div class="header-bg-shape shape-1"></div>
    <div class="header-bg-shape shape-2"></div>
    
    <div class="container navbar-custom">
        <div class="d-flex align-items-center justify-content-between">
            <a href="#" class="brand-logo">
                <img src="{{ asset('logoskolabs.png') }}" alt="Logo" style="height: 24px; margin-right: 8px; vertical-align: middle; filter: brightness(0) invert(1);">
                SKOLABS <span>CBT</span>
            </a>
            <div class="user-nav-card shadow-sm">
                <div class="user-avatar">{{ substr($siswa->name, 0, 1) }}</div>
                <div class="d-none d-md-block">
                    <div style="font-size: 0.85rem; font-weight: 600; line-height: 1;">{{ $siswa->name }}</div>
                    <div style="font-size: 0.7rem; opacity: 0.7;">Participant</div>
                </div>
                <div style="width: 1px; height: 20px; background: rgba(255,255,255,0.2); margin: 0 5px;"></div>
                <form action="{{ route('siswa.logout') }}" method="POST" id="logout-form" style="display: none;">@csrf</form>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-link">Keluar</a>
            </div>
        </div>
    </div>

    <div class="container pt-4 pb-2">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="hero-title" style="font-weight: 800; font-size: 2.2rem; letter-spacing: -1px; margin-bottom: 10px; color: #ffffff;">Halo, {{ explode(' ', $siswa->name)[0] }}!</h1>
                <p class="hero-subtitle" style="font-size: 1rem; opacity: 0.9; margin-bottom: 0;">Semoga sukses dengan ujian hari ini. Tetap kejujuran adalah prioritas utama.</p>
                <div class="mt-3 d-flex flex-wrap gap-2">
                    <div class="hero-badge" style="background: rgba(255,255,255,0.15); padding: 5px 14px; border-radius: 50px; font-size: 0.82rem; font-weight: 500;">
                        💳 <span style="opacity: 0.7; margin-left: 4px;">No Peserta:</span> <b>{{ $siswa->nomer_ujian }}</b>
                    </div>
                    @if($siswa->group)
                    <div class="hero-badge" style="background: rgba(255,255,255,0.15); padding: 5px 14px; border-radius: 50px; font-size: 0.82rem; font-weight: 500;">
                        🏫 <span style="opacity: 0.7; margin-left: 4px;">Grup:</span> <b>{{ $siswa->group->name }}</b>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<div class="container stats-container">
    <div class="row g-3">
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon icon-ongoing">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $ongoing->count() }}</h3>
                    <p>Berlangsung</p>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon icon-upcoming">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $upcoming->count() }}</h3>
                    <p>Mendatang</p>
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-icon icon-finished">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $finished->count() }}</h3>
                    <p>Selesai</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="pt-4 pb-5">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 px-4 py-3 mb-4">
                <div class="d-flex align-items-center">
                    <div style="font-size: 1.5rem; margin-right: 15px;">✅</div>
                    <b>{{ session('success') }}</b>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 px-4 py-3 mb-4">
                <div class="d-flex align-items-center">
                    <div style="font-size: 1.5rem; margin-right: 15px;">❌</div>
                    <b>{{ session('error') }}</b>
                </div>
            </div>
        @endif

        {{-- SEDANG BERLANGSUNG --}}
        @if($ongoing->count() > 0)
        <div class="section-header">
            <h2>Ujian Berlangsung</h2>
            <div class="line"></div>
        </div>
        @foreach($ongoing as $exam)
        <div class="exam-card ongoing">
            <div class="card-status-indicator"></div>
            <div class="card-body-content">
                <span class="exam-tag tag-ongoing">🔴 Sedang Berlangsung</span>
                <div class="exam-title">{{ $exam->name }}</div>
                <div class="exam-event">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    {{ $exam->examEvent->name }}
                </div>
                <div class="exam-details">
                    <div class="detail-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <b>{{ $exam->duration }} Menit</b>
                    </div>
                    <div class="detail-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Hingga <b>{{ isset($siswaSession) ? \Carbon\Carbon::parse($siswaSession->end_time)->format('H:i') : $exam->end_at->format('H:i') }} WIB</b>
                    </div>
                </div>
            </div>
            <div class="card-action-box">
                @if($exam->token)
                    <input type="text" id="token_{{ $exam->id }}" class="token-input" placeholder="Masukkan Token" autocomplete="off">
                @endif
                <form action="{{ route('siswa.exam.start', $exam->id) }}" method="POST" id="form_start_{{ $exam->id }}" style="width: 100%;">
                    @csrf
                    <input type="hidden" name="token" id="hidden_token_{{ $exam->id }}">
                    <button type="submit" class="btm-mulai" onclick="return checkToken({{ $exam->id }}, '{{ $exam->token }}', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                        Mulai Ujian
                    </button>
                </form>
            </div>
        </div>
        @endforeach
        @endif

        {{-- MENDATANG --}}
        @if($upcoming->count() > 0)
        <div class="section-header">
            <h2>Ujian Mendatang</h2>
            <div class="line"></div>
        </div>
        @foreach($upcoming as $exam)
        <div class="exam-card upcoming">
            <div class="card-status-indicator"></div>
            <div class="card-body-content">
                <span class="exam-tag tag-upcoming">🕒 Baru Akan Dimulai</span>
                <div class="exam-title text-muted">{{ $exam->name }}</div>
                <div class="exam-event">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    {{ $exam->examEvent->name }}
                </div>
                <div class="exam-details">
                    <div class="detail-item">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Dimulai: <b>{{ $exam->start_at->format('d M, H:i') }} WIB</b>
                    </div>
                </div>
            </div>
            <div class="card-action-box">
                <div class="btm-disabled">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Terkunci
                </div>
                <div class="countdown-timer" data-end="{{ $exam->start_at->toIso8601String() }}">Menghitung...</div>
            </div>
        </div>
        @endforeach
        @endif

        {{-- SELESAI --}}
        @if($finished->count() > 0)
        <div class="section-header">
            <h2>Ujian Selesai</h2>
            <div class="line"></div>
        </div>
        @foreach($finished as $exam)
        @php $res = isset($results) ? $results->get($exam->id) : null; $isBlocked = $res && $res->status === 'blocked'; @endphp
        <div class="exam-card finished" style="opacity: 0.7;">
            <div class="card-status-indicator" {!! $isBlocked ? 'style="background:#fee2e2;"' : '' !!}></div>
            <div class="card-body-content">
                @if($isBlocked)
                    <span class="exam-tag tag-blocked">⚠️ Diblokir</span>
                @else
                    <span class="exam-tag tag-finished">✅ Selesai</span>
                @endif
                <div class="exam-title">{{ $exam->name }}</div>
                <div class="exam-event">{{ $exam->examEvent->name }}</div>
                <div class="exam-details">
                    <div class="detail-item">
                        @if($isBlocked)
                            <span class="text-danger">Ujian diblokir. Hubungi Pengawas untuk reset.</span>
                        @else
                            Selesai pada {{ $exam->end_at->format('d/m/Y') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif

        @if($schedules->isEmpty())
        <div class="text-center py-5">
            <div style="font-size: 4rem; opacity: 0.2; margin-bottom: 20px;">📋</div>
            <h3 style="color: #64748b;">Tidak Ada Jadwal Ujian</h3>
            <p style="color: #94a3b8;">Saat ini belum ada jadwal ujian yang ditugaskan untuk Anda.</p>
        </div>
        @endif
    </div>
</div>

<script>
// ── Countdown timers ──
function formatCountdown(diff) {
    if (diff <= 0) return 'Memulai...';
    const h = Math.floor(diff / 3600);
    const m = Math.floor((diff % 3600) / 60);
    const s = diff % 60;
    if (h > 0) return `Buka dalam ${h}j ${m}m ${s}s`;
    if (m > 0) return `Buka dalam ${m}m ${s}s`;
    return `Buka dalam ${s}s`;
}

document.querySelectorAll('[data-end]').forEach(el => {
    const end = new Date(el.getAttribute('data-end')).getTime();
    function tick() {
        const diff = Math.floor((end - Date.now()) / 1000);
        el.textContent = formatCountdown(diff);
        if (diff > 0) setTimeout(tick, 1000);
        else location.reload();
    }
    tick();
});

// ── Token check ──
function checkToken(examId, requiredToken, btn) {
    if (!requiredToken) return true;
    const input = document.getElementById('token_' + examId);
    if (!input) return true;
    
    const enteredToken = input.value.trim().toUpperCase();
    if (enteredToken === requiredToken.trim().toUpperCase()) {
        const hiddenInput = document.getElementById('hidden_token_' + examId);
        if(hiddenInput) hiddenInput.value = enteredToken;
        return true;
    }
    
    input.style.borderColor = '#ef4444';
    input.value = '';
    input.placeholder = 'Token Salah!';
    input.focus();
    return false;
}
</script>

<script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
</body>
</html>

