<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SKOLABS CBT - {{ $schedule->name }}</title>
    @php
        $app_logo = \App\Models\Setting::get('app_logo');
        $favicon = $app_logo ? \Storage::url($app_logo) : asset('logoskolabs.png');
    @endphp
    <link rel="shortcut icon" href="{{ $favicon }}" />
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#1a4da1">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CBT Siswa">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
            user-select: none !important;
            -webkit-touch-callout: none !important;
            overscroll-behavior: none !important;
            overscroll-behavior-y: none !important;
            touch-action: pan-y !important;
        }

        body { 
            background: #e9ecef; 
            font-family: 'Roboto', sans-serif; 
            margin: 0; 
            color: #333;
        }

        /* Proteksi Layar saat Blur / Screenshot / Pindah Aplikasi */
        body.screen-protected {
            filter: blur(40px) brightness(0.1) !important;
            opacity: 0.05 !important;
            transition: filter 0.05s ease, opacity 0.05s ease !important;
            pointer-events: none !important;
        }
        
        /* â”€â”€ Header â”€â”€ */
        .header {
            background: linear-gradient(90deg, #1a4da1 0%, #0d3a85 100%);
            color: #fff;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: "";
            position: absolute;
            left: -50px;
            top: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 1;
        }
        .header-logo {
            width: 50px;
            height: 50px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #1a4da1;
            font-size: 1.2rem;
        }
        .header-title h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-title p {
            margin: 0;
            font-size: 0.85rem;
            opacity: 0.8;
            letter-spacing: 0.5px;
        }
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
            z-index: 1;
        }
        .user-info {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }
        .user-info .name {
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
        }
        .user-icon {
            background: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* â”€â”€ Main Container â”€â”€ */
        .main-container {
            max-width: 1200px;
            margin: 20px auto 40px;
            padding: 0 20px;
            position: relative;
            z-index: 2;
        }
        .exam-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
            min-height: 500px;
        }

        /* â”€â”€ Card Header â”€â”€ */
        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        .soal-info h2 {
            margin: 0;
            font-size: 1.8rem;
            color: #444;
            font-weight: 400;
        }
        .soal-info span {
            font-size: 0.9rem;
            color: #888;
            font-weight: 500;
        }
        
        .timer-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .timer-box {
            border: 2px solid #dc3545;
            border-radius: 8px;
            padding: 8px 15px;
            color: #333;
            font-weight: 500;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 180px;
            justify-content: center;
        }
        .timer-label {
            font-size: 0.8rem;
            color: #666;
        }
        .btn-info-soal {
            background: #1e40af;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .btn-daftar-soal {
            background: #1e40af;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        /* â”€â”€ Font Size Selector â”€â”€ */
        .font-selector {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            color: #666;
        }
        .font-btn {
            cursor: pointer;
            padding: 2px 5px;
            border-bottom: 2px solid transparent;
        }
        .font-btn:hover { color: #1e40af; }
        .font-btn.active { border-color: #1e40af; color: #1e40af; }

        /* â”€â”€ Question Content â”€â”€ */
        .question-box {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 30px;
            margin-bottom: 30px;
            background: #f8f9fa;
            max-height: 65vh;
            overflow-y: auto;
        }
        .question-text {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 25px;
            overflow-x: auto;
        }
        .question-text img { max-width: 100%; height: auto; border-radius: 4px; }
        
        .options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .option-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            cursor: pointer;
        }
        .option-circle {
            width: 28px;
            height: 28px;
            border: 1px solid #adb5bd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: #6c757d;
            background: #fff;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .option-item:hover .option-circle {
            background: #e9ecef;
            border-color: #0d6efd;
        }
        .option-item.selected .option-circle {
            background: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        .option-text {
            padding-top: 3px;
            font-size: 1rem;
            color: #444;
        }
        .option-item input { display: none; }

        /* â”€â”€ Footer Actions â”€â”€ */
        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .btn-nav {
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            border: none;
            transition: opacity 0.2s;
        }
        .btn-nav:hover { opacity: 0.9; }
        .btn-nav:disabled { opacity: 0.5; cursor: not-allowed; }
        
        .btn-prev { background: #f06292; color: #fff; }
        .btn-ragu { background: #ffb74d; color: #fff; }
        .btn-ragu.flagged { background: #f57c00; box-shadow: inset 0 2px 4px rgba(0,0,0,0.2); }
        .btn-simpan { background: #4caf50; color: #fff; }
        .btn-next { background: #03a9f4; color: #fff; }
        .btn-selesai { background: #4caf50; color: #fff; }

        /* â”€â”€ Sidebar (Daftar Soal) â”€â”€ */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            z-index: 1000;
        }
        .sidebar {
            position: fixed;
            right: -350px;
            top: 0;
            width: 350px;
            height: 100%;
            background: #fff;
            z-index: 1001;
            transition: right 0.3s ease;
            padding: 30px;
            box-shadow: -5px 0 20px rgba(0,0,0,0.1);
        }
        .sidebar.active { right: 0; }
        .sidebar-overlay.active { display: block; }
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .num-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 8px;
        }
        .num-btn {
            border: 1px solid #dee2e6;
            background: #fff;
            aspect-ratio: 1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
        }
        .num-btn.current { border: 2px solid #03a9f4; color: #03a9f4; }
        .num-btn.answered { background: #4caf50; color: #fff; border: none; }
        .num-btn.flagged { background: #ffb74d; color: #fff; border: none; }
        
        /* â”€â”€ Modals â”€â”€ */
        .modal-wrap {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .modal-wrap.show { display: flex; }
        .modal-box {
            background: #fff;
            width: 450px;
            border-radius: 12px;
            padding: 35px;
            text-align: center;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        .modal-box h3 { margin-top: 0; color: #333; }
        .modal-box p { color: #666; margin-bottom: 25px; line-height: 1.5; }
        .modal-btns { display: flex; gap: 12px; }
        .modal-btns button {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-cancel { background: #f8f9fa; color: #333; border: 1px solid #dee2e6 !important; }
        .btn-confirm { background: #1e40af; color: #fff; }

        @media (max-width: 768px) {
            .header { padding: 10px 15px; height: auto; min-height: 70px; flex-wrap: nowrap; }
            .header-logo { width: 35px; height: 35px; margin-right: 10px !important; }
            .header-left img { height: 35px !important; margin-right: 10px !important; }
            .header-title h1 { font-size: 1rem; }
            .header-title p { font-size: 0.65rem; }
            .header-right { gap: 10px; }
            .user-info .name { font-size: 0.75rem; max-width: 80px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
            .user-icon { width: 35px; height: 35px; }
            .main-container { padding: 0 10px; margin-top: 10px; }
            .exam-card { padding: 15px; border-radius: 8px; }
            .card-top { flex-direction: column; gap: 15px; align-items: flex-start; }
            .timer-container { width: 100%; display: flex; flex-direction: row; gap: 10px; justify-content: space-between; }
            .timer-box { width: auto; flex: 1; padding: 10px; font-size: 1.1rem; }
            .btn-daftar-soal { flex: 1; padding: 10px; font-size: 0.85rem; justify-content: center; }
            .card-footer { flex-wrap: wrap; gap: 10px; }
            .btn-nav { flex: 1 1 45%; justify-content: center; font-size: 0.85rem; padding: 10px; }
        }
        .swal2-popup.rounded-4 .swal2-icon { transform: scale(0.6); margin-top: 0; margin-bottom: 0; }
        
        #fullscreen-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(0, 0, 0, 0.95); z-index: 99999;
            display: flex; align-items: center; justify-content: center;
        }
        body.is-fullscreen #fullscreen-overlay {
            display: none;
        }

        /* ── Offline Network Overlay ── */
        #offline-overlay {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
            background: rgba(15, 23, 42, 0.88); backdrop-filter: blur(5px);
            z-index: 1000000;
            display: none; align-items: center; justify-content: center;
        }
        .offline-card {
            border: 1px solid rgba(220, 53, 69, 0.2);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: offlineBounceIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes offlineBounceIn {
            from { opacity: 0; transform: scale(0.92); }
            to { opacity: 1; transform: scale(1); }
        }
        .offline-icon-pulse {
            width: 76px; height: 76px; border-radius: 50%;
            background: #ffebe9; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
            animation: offlinePulse 2s infinite;
            margin: 0 auto;
        }
        @keyframes offlinePulse {
            0% { transform: scale(0.96); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
            70% { transform: scale(1); box-shadow: 0 0 0 14px rgba(220, 53, 69, 0); }
            100% { transform: scale(0.96); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
    </style>
</head>
<body>

<div id="offline-overlay">
    <div class="offline-card text-center bg-white p-4 rounded-4 shadow-lg" style="max-width: 440px; margin: 0 20px;">
        <div class="mb-3">
            <div class="offline-icon-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#dc3545" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                    <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"></path>
                    <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"></path>
                    <path d="M10.71 5.05A16 16 0 0 1 22.58 9"></path>
                    <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"></path>
                    <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                    <line x1="12" y1="20" x2="12.01" y2="20"></line>
                </svg>
            </div>
        </div>
        <h5 class="fw-bold text-danger mb-2">Koneksi Internet Terputus</h5>
        <p class="text-muted mb-3" style="font-size: 0.9rem; line-height: 1.5;">
            Jaringan internet Anda terputus. <b>Jawaban Anda tersimpan aman</b> di perangkat ini.
            Ujian akan otomatis dapat dilanjutkan segera setelah internet terhubung kembali.
        </p>
        <div id="offline-status-box" class="p-2 mb-3 rounded-3 bg-light border text-muted small d-flex align-items-center justify-content-center">
            <span class="spinner-border spinner-border-sm text-danger me-2" role="status"></span>
            <span id="offline-status-text">Menunggu jaringan internet kembali...</span>
        </div>
        <button type="button" id="btn-retry-connection" class="btn btn-outline-danger btn-sm w-100 fw-bold py-2 mb-2 d-flex align-items-center justify-content-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            Coba Hubungkan Sekarang
        </button>
        <div class="alert alert-warning py-1 px-2 mb-0 text-start" style="font-size: 0.78rem;">
            ⚠️ <b>Penting:</b> Jangan me-refresh atau menutup tab ujian ini agar tidak keluar dari ujian.
        </div>
    </div>
</div>

<div id="fullscreen-overlay">
    <div class="text-center bg-white p-4 rounded shadow" style="max-width: 400px; margin: 0 20px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #1a4da1; margin-bottom: 20px;"><path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path></svg>
        <h4 class="mb-3 fw-bold" style="color: #333;">Layar Penuh Diperlukan</h4>
        <p class="text-muted mb-4" style="font-size: 0.9rem;">Ujian ini harus dikerjakan dalam mode layar penuh (Fullscreen) untuk keamanan dan menghindari perpindahan tab.</p>
        <button id="btn-fullscreen" class="btn btn-primary w-100" style="background: #1a4da1; border:none; padding: 12px; font-weight: 600;">
            Masuk Layar Penuh & Lanjut
        </button>
    </div>
</div>

<header class="header">
    <div class="header-left">
        @php
            $app_logo = \App\Models\Setting::get('app_logo');
            $app_name = \App\Models\Setting::get('app_name', 'SKOLABS CBT');
        @endphp
        @if($app_logo)
            <img src="{{ Storage::url($app_logo) }}" alt="Logo" style="height: 40px; margin-right: 15px; border-radius: 50%; object-fit: cover;">
        @else
            <img src="{{ asset('logoskolabs.png') }}" alt="Logo" style="height: 40px; margin-right: 15px; object-fit: contain; filter: brightness(0) invert(1);">
        @endif
        <div class="header-title">
            <h1 style="color: #ffffff;">{{ $app_name }}</h1>
            <p style="color: #e0e0e0;">Computer Based Test Application</p>
        </div>
    </div>
    <div class="header-right">
        <div class="user-info">
            <div class="name">{{ $siswa->name }}</div>
            <a href="javascript:void(0)" onclick="confirmLogout()" class="btn btn-sm btn-danger py-0 px-2" style="font-size: 0.7rem; border-radius: 4px;">Log Out</a>
        </div>
        <div class="user-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        </div>
    </div>
    <form id="logout-form" action="{{ route('siswa.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
</header>

<div class="main-container">
    <div class="exam-card">
        <div class="card-top">
            <div class="soal-info">
                <h2>Soal nomor {{ $no }}</h2>
                <span>{{ $schedule->bankSoal->subject->name ?? 'Mata Pelajaran Umum' }}</span>
            </div>
            
            <div class="timer-container">
                <div class="timer-box" id="timerBox">
                    <span class="timer-label">Sisa Waktu:</span>
                    <span id="timerDisplay">00:00:00</span>
                </div>
                <button type="button" class="btn-daftar-soal" onclick="toggleSidebar()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Daftar Soal
                </button>
            </div>
        </div>

        <div class="font-selector">
            <span>Ukuran font soal:</span>
            <span class="font-btn" onclick="changeFontSize(0.9)">A</span>
            <span class="font-btn active" onclick="changeFontSize(1.1)">A</span>
            <span class="font-btn" onclick="changeFontSize(1.3)">A</span>
        </div>

        <div id="loadingOverlay" style="display:none; position:absolute; inset:0; background:rgba(255,255,255,0.7); z-index:100; align-items:center; justify-content:center; border-radius:12px;">
            <div class="spinner-border text-primary" role="status"></div>
        </div>

                <div id="questionContainer" style="display:none;">
            <input type="hidden" id="current_question_id" value="">
            <input type="hidden" id="current_no" value="{{ $no }}">
            <input type="hidden" id="is_flagged_val" value="0">

            <div class="question-box">
                <div class="question-text" id="qText"></div>
                <div class="options-list" id="optionsContainer"></div>
            </div>

            <div class="card-footer">
                <button type="button" id="btnPrev" class="btn-nav btn-prev" onclick="navigate('prev')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Sebelumnya
                </button>
                <button type="button" id="flagBtn" class="btn-nav btn-ragu" onclick="toggleFlag()">Ragu - Ragu</button>
                <button type="button" id="btnNext" class="btn-nav btn-next" onclick="navigate('next')">
                    Selanjutnya
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
                <button type="button" id="btnFinish" class="btn-nav btn-selesai" onclick="confirmFinish()" style="display:none;">Selesai Ujian</button>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Sidebar Daftar Soal --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4 style="margin:0">Daftar Soal</h4>
        <button type="button" onclick="toggleSidebar()" style="background:none;border:none;font-size:1.5rem;cursor:pointer">&times;</button>
    </div>
    <div class="num-grid" id="questionGrid">
        @foreach($questionIds as $idx => $qid)
        @php
            $btnNo = $idx + 1;
            $btnClass = '';
            if ($btnNo == $no) $btnClass = 'current';
            elseif(isset($answers[$qid]) && $answers[$qid]->is_flagged) $btnClass = 'flagged';
            elseif(isset($answers[$qid]) && !is_null($answers[$qid]->answer)) $btnClass = 'answered';
        @endphp
        <button type="button" class="num-btn {{ $btnClass }}" id="grid-{{ $btnNo }}" onclick="gotoQuestion({{ $btnNo }})">{{ $btnNo }}</button>
        @endforeach
    </div>
</div>

{{-- Modal Konfirmasi Selesai --}}
<div class="modal-wrap" id="finishModal">
    <div class="modal-box">
        <div style="color: #1e40af; margin-bottom: 20px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        <h3>Akhiri Ujian?</h3>
        <p>Pastikan semua soal sudah terjawab sebelum mengakhiri ujian. Jawaban tidak dapat diubah setelah selesai.</p>
        <div id="finishSummary" style="background:#f1f3f5; padding:15px; border-radius:8px; margin-bottom:25px; text-align:left; font-size:0.9rem;">
        </div>
        <div class="modal-btns">
            <button type="button" class="btn-cancel" id="btnCancelFinish" onclick="document.getElementById('finishModal').classList.remove('show')">Kembali</button>
            <button type="button" class="btn-confirm" id="btnConfirmFinish" onclick="handleFinishSubmit(this)">Ya, Selesai</button>
        </div>
    </div>
</div>

<form action="{{ route('siswa.exam.finish', $schedule->id) }}" method="POST" id="finishForm" style="display:none;">
    @csrf
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    // ── Global State ──
    let currentNo = {{ $no }};
    const totalSoal = {{ $total }};
    const scheduleId = "{{ $schedule->id }}";
    let isSaving = false;

    // ── Manajemen Jaringan (Offline / Online) ──
    let isOfflineModalOpen = false;
    let offlineCheckInterval = null;
    let pendingQuestionNo = null;
    let pendingSavePayload = null;

    function showOfflineModal() {
        if (isOfflineModalOpen) return;
        isOfflineModalOpen = true;

        const overlay = document.getElementById('offline-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
            const statusBox = document.getElementById('offline-status-box');
            if (statusBox) {
                statusBox.className = 'p-2 mb-3 rounded-3 bg-light border text-muted small d-flex align-items-center justify-content-center';
                statusBox.innerHTML = '<span class="spinner-border spinner-border-sm text-danger me-2" role="status"></span><span id="offline-status-text">Menunggu jaringan internet kembali...</span>';
            }
        }

        if (!offlineCheckInterval) {
            offlineCheckInterval = setInterval(testConnectionAndResume, 3000);
        }
    }

    function hideOfflineModal() {
        if (!isOfflineModalOpen) return;

        if (offlineCheckInterval) {
            clearInterval(offlineCheckInterval);
            offlineCheckInterval = null;
        }

        const statusBox = document.getElementById('offline-status-box');
        if (statusBox) {
            statusBox.className = 'p-2 mb-3 rounded-3 bg-success bg-opacity-10 border border-success text-success small d-flex align-items-center justify-content-center';
            statusBox.innerHTML = '<span class="fw-bold text-success">✓ Terhubung Kembali! Melanjutkan ujian...</span>';
        }

        setTimeout(() => {
            const overlay = document.getElementById('offline-overlay');
            if (overlay) overlay.style.display = 'none';
            isOfflineModalOpen = false;

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Koneksi Pulih',
                text: 'Internet terhubung kembali. Selamat melanjutkan ujian!',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            // 1. Simpan ulang jawaban yang sempat tertunda
            if (pendingSavePayload) {
                doSaveAnswer(true);
            }

            // 2. Muat ulang nomor soal yang tertunda
            if (pendingQuestionNo !== null) {
                const targetNo = pendingQuestionNo;
                pendingQuestionNo = null;
                gotoQuestion(targetNo);
            }
        }, 700);
    }

    function testConnectionAndResume() {
        fetch(`{{ url('ping') }}?_t=${Date.now()}`, {
            method: 'GET',
            cache: 'no-store',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (res.ok) {
                hideOfflineModal();
            }
        })
        .catch(() => {
            // Masih offline
        });
    }

    window.addEventListener('offline', () => {
        showOfflineModal();
    });

    window.addEventListener('online', () => {
        testConnectionAndResume();
    });

    // ── Timer Logic ──
    let remaining = {{ $remainingSec }};
    const timerDisplay = document.getElementById('timerDisplay');
    const timerBox = document.getElementById('timerBox');

    function formatTime(sec) {
        const h = Math.floor(sec / 3600);
        const m = Math.floor((sec % 3600) / 60);
        const s = sec % 60;
        return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
    }

    const timerInterval = setInterval(() => {
        remaining--;
        timerDisplay.textContent = formatTime(remaining);
        if (remaining <= 300) timerBox.style.borderColor = '#dc3545';
        if (remaining <= 0) {
            clearInterval(timerInterval);
            if(typeof cheatDetectorActive !== 'undefined') cheatDetectorActive = false;
            if (isOfflineModalOpen) {
                const checkSubmit = setInterval(() => {
                    if (!isOfflineModalOpen) {
                        clearInterval(checkSubmit);
                        document.getElementById('finishForm').submit();
                    }
                }, 1000);
            } else {
                document.getElementById('finishForm').submit();
            }
        }
    }, 1000);
    timerDisplay.textContent = formatTime(remaining);

    // ── Sidebar Logic ──
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }

    // ── Font Size Logic ──
    function changeFontSize(size) {
        document.getElementById('qText').style.fontSize = size + 'rem';
        document.querySelectorAll('.font-btn').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }

    // ── Core AJAX Logic ──
    document.addEventListener('DOMContentLoaded', () => {
        gotoQuestion(currentNo);
    });

    function navigate(dir) {
        let next = currentNo;
        if (dir === 'next') next++;
        if (dir === 'prev') next--;
        
        if (next >= 1 && next <= totalSoal) {
            gotoQuestion(next);
        }
    }

    function gotoQuestion(no) {
        if (no === currentNo && document.getElementById('qText').innerHTML !== '') return;
        
        showLoading(true);
        // Correcting the URL to match Route::get('/exam/{schedule}/soal/{no}', ...)
        fetch(`{{ url('siswa/exam') }}/${scheduleId}/soal/${no}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async res => {
            const text = await res.text();
            if (!res.ok) {
                console.group("Error Response");
                console.error("Status:", res.status);
                console.error("Body:", text);
                console.groupEnd();
                throw new Error(`HTTP ${res.status}: ${text.substring(0, 100)}`);
            }
            try {
                return JSON.parse(text);
            } catch(e) {
                console.error("JSON Parse Error. Raw body:", text);
                throw new Error("Invalid server response format");
            }
        })
        .then(data => {
            pendingQuestionNo = null;
            renderQuestion(data);
            if (document.getElementById('sidebar').classList.contains('active')) toggleSidebar();
        })
        .catch(err => {
            console.error("Fetch error:", err);
            pendingQuestionNo = no;
            showOfflineModal();
        })
        .finally(() => showLoading(false));
    }

    function renderQuestion(data) {
        document.getElementById('questionContainer').style.display = 'block';
        currentNo = data.current_no;
        document.getElementById('current_no').value = currentNo;
        document.getElementById('current_question_id').value = data.question.id;
        document.querySelector('.soal-info h2').textContent = `Soal nomor ${currentNo}`;
        
        let qTextHtml = data.question.question_text;
        if(data.question.type === 'LS' && data.question.audio_path) {
            qTextHtml += `<div class="mt-3"><audio controls controlsList="nodownload"><source src="/storage/${data.question.audio_path}" type="audio/mpeg">Your browser does not support the audio element.</audio></div>`;
        }
        document.getElementById('qText').innerHTML = qTextHtml;
        
        const isFlagged = data.current_flagged;
        document.getElementById('is_flagged_val').value = isFlagged ? '1' : '0';
        document.getElementById('flagBtn').classList.toggle('flagged', isFlagged);
        
        const container = document.getElementById('optionsContainer');
        container.innerHTML = '';
        const qType = data.question.type;
        const curAns = data.current_answer;

        if (['PG', 'LS', 'BS', 'ST'].includes(qType)) {
            const list = data.question.options_list || [];
            if (list.length > 0) {
                list.forEach(item => {
                    const label = item.label;
                    const key = item.key;
                    const text = item.text;
                    const isSelected = curAns === key;
                    const optionDiv = document.createElement('div');
                    optionDiv.className = `option-item ${isSelected ? 'selected' : ''}`;
                    optionDiv.onclick = () => {
                        document.querySelectorAll('.option-item').forEach(el => el.classList.remove('selected'));
                        optionDiv.classList.add('selected');
                        const r = document.getElementById('radio_' + key);
                        if(r) { r.checked = true; saveAnswer(); }
                    };
                    optionDiv.innerHTML = `
                        <input type="radio" name="answer" value="${key}" id="radio_${key}" ${isSelected ? 'checked' : ''} style="display:none;">
                        <div class="option-circle">${label}</div>
                        <div class="option-text">${text}</div>
                    `;
                    container.appendChild(optionDiv);
                });
            } else {
                let opts = qType === 'BS' || qType === 'ST' ? ['a', 'b'] : ['a', 'b', 'c', 'd', 'e'];
                opts.forEach(opt => {
                    const text = data.question[`option_${opt}`];
                    if (text) {
                        const key = opt.toUpperCase();
                        const isSelected = curAns === key;
                        const optionDiv = document.createElement('div');
                        optionDiv.className = `option-item ${isSelected ? 'selected' : ''}`;
                        optionDiv.onclick = () => {
                            document.querySelectorAll('.option-item').forEach(el => el.classList.remove('selected'));
                            optionDiv.classList.add('selected');
                            const r = document.getElementById('radio_' + key);
                            if(r) { r.checked = true; saveAnswer(); }
                        };
                        optionDiv.innerHTML = `
                            <input type="radio" name="answer" value="${key}" id="radio_${key}" ${isSelected ? 'checked' : ''} style="display:none;">
                            <div class="option-circle">${key}</div>
                            <div class="option-text">${text}</div>
                        `;
                        container.appendChild(optionDiv);
                    }
                });
            }
        } else if (qType === 'PGK') {
            let ansArr = Array.isArray(curAns) ? curAns : [];
            const list = data.question.options_list || [];
            if (list.length > 0) {
                list.forEach(item => {
                    const label = item.label;
                    const key = item.key;
                    const text = item.text;
                    const isSelected = ansArr.includes(key);
                    const optionDiv = document.createElement('div');
                    optionDiv.className = `option-item ${isSelected ? 'selected' : ''}`;
                    optionDiv.onclick = (e) => {
                        if(e.target.tagName !== 'INPUT') {
                            const cb = document.getElementById('chk_' + key);
                            cb.checked = !cb.checked;
                        }
                        const cb = document.getElementById('chk_' + key);
                        if(cb.checked) optionDiv.classList.add('selected');
                        else optionDiv.classList.remove('selected');
                        saveAnswer();
                    };
                    optionDiv.innerHTML = `
                        <input type="checkbox" name="answer[]" value="${key}" id="chk_${key}" ${isSelected ? 'checked' : ''} style="display:none;">
                        <div class="option-circle" style="border-radius:4px;">${label}</div>
                        <div class="option-text">${text}</div>
                    `;
                    container.appendChild(optionDiv);
                });
            } else {
                ['a', 'b', 'c', 'd', 'e'].forEach(opt => {
                    const text = data.question[`option_${opt}`];
                    if (text) {
                        const key = opt.toUpperCase();
                        const isSelected = ansArr.includes(key);
                        const optionDiv = document.createElement('div');
                        optionDiv.className = `option-item ${isSelected ? 'selected' : ''}`;
                        optionDiv.onclick = (e) => {
                            if(e.target.tagName !== 'INPUT') {
                                const cb = document.getElementById('chk_' + key);
                                cb.checked = !cb.checked;
                            }
                            const cb = document.getElementById('chk_' + key);
                            if(cb.checked) optionDiv.classList.add('selected');
                            else optionDiv.classList.remove('selected');
                            saveAnswer();
                        };
                        optionDiv.innerHTML = `
                            <input type="checkbox" name="answer[]" value="${key}" id="chk_${key}" ${isSelected ? 'checked' : ''} style="display:none;">
                            <div class="option-circle" style="border-radius:4px;">${key}</div>
                            <div class="option-text">${text}</div>
                        `;
                        container.appendChild(optionDiv);
                    }
                });
            }
        } else if (qType === 'MJ') {
            const left = data.question.mj_left || [];
            const right = data.question.mj_right || [];
            let ansMap = (typeof curAns === 'object' && curAns !== null) ? curAns : {};
            
            let usedRight = Object.values(ansMap);
            // Count occurrences to correctly place remaining items in pool
            let poolRight = [...right];
            usedRight.forEach(used => {
                let i = poolRight.indexOf(used);
                if(i > -1) poolRight.splice(i, 1);
            });

            let html = '<div class="row"><div class="col-md-7"><h6 class="text-muted fw-bold mb-3">Pernyataan</h6><div class="list-group mb-4">';
            
            left.forEach((lItem, idx) => {
                let sel = ansMap[idx] || '';
                html += `
                <div class="list-group-item d-flex flex-column flex-md-row align-items-start align-items-md-center mb-3 border rounded shadow-sm" style="background:#f8f9fa;">
                    <div style="flex:1; margin-right:15px; margin-bottom: 10px;" class="fw-bold text-dark">${idx + 1}. ${lItem}</div>
                    <div class="mj-dropzone" data-idx="${idx}" style="width: 100%; max-width: 250px; min-height: 48px; border: 2px dashed #adb5bd; background: #fff; border-radius: 6px; display:flex; align-items:center; justify-content:center; padding: 4px;">
                `;
                if (sel) {
                    html += `<div class="p-2 border rounded bg-white text-primary shadow-sm fw-bold w-100 text-center mj-item" data-val="${sel}" style="cursor:grab;">${sel}</div>`;
                }
                html += `    </div>
                </div>`;
            });
            html += `</div></div><div class="col-md-5"><h6 class="text-muted fw-bold mb-3">Pilihan Jawaban (Tarik & Letakkan)</h6><div id="mj-pool" class="p-3 border rounded shadow-inner" style="min-height: 150px; background:#e9ecef; display: flex; flex-direction: column; gap: 8px;">`;
            
            poolRight.forEach(rItem => {
                html += `<div class="p-2 border rounded bg-white text-primary shadow-sm fw-bold text-center mj-item" data-val="${rItem}" style="cursor:grab;">${rItem}</div>`;
            });

            html += `</div></div></div>`;
            container.innerHTML = html;

            setTimeout(() => {
                if(typeof Sortable !== 'undefined') {
                    Sortable.create(document.getElementById('mj-pool'), {
                        group: 'mj',
                        animation: 150,
                        onEnd: saveAnswer
                    });

                    document.querySelectorAll('.mj-dropzone').forEach(zone => {
                        Sortable.create(zone, {
                            group: 'mj',
                            animation: 150,
                            onAdd: function(evt) {
                                if (zone.children.length > 1) {
                                    let oldItem = evt.item === zone.children[0] ? zone.children[1] : zone.children[0];
                                    document.getElementById('mj-pool').appendChild(oldItem);
                                }
                            },
                            onEnd: saveAnswer
                        });
                    });
                }
            }, 100);
        } else if (qType === 'MT') {
            const items = data.question.mt_items || [];
            let ansArr = Array.isArray(curAns) ? curAns : [];
            
            let html = '<div class="table-responsive"><table class="table table-bordered bg-white"><tbody>';
            items.forEach((item, idx) => {
                let sel = ansArr[idx] || '';
                html += `<tr>
                    <td class="align-middle" width="10%">Urutan ${idx + 1}</td>
                    <td class="align-middle">
                        <select class="form-select mt-select" data-idx="${idx}" onchange="saveAnswer()">
                            <option value="">-- Pilih Item --</option>`;
                items.forEach(optItem => {
                    html += `<option value="${optItem}" ${sel === optItem ? 'selected' : ''}>${optItem}</option>`;
                });
                html += `</select></td></tr>`;
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        } else {
            const textarea = document.createElement('textarea');
            textarea.id = 'essayAnswer';
            textarea.className = 'form-control';
            textarea.rows = 8;
            textarea.placeholder = "Tuliskan jawaban Anda di sini...";
            textarea.value = curAns || '';
            textarea.onblur = saveAnswer;
            container.appendChild(textarea);
        }

        document.getElementById('btnPrev').disabled = (currentNo <= 1);
        document.getElementById('btnNext').style.display = (currentNo >= totalSoal) ? 'none' : 'flex';
        document.getElementById('btnFinish').style.display = (currentNo < totalSoal) ? 'none' : 'flex';

        document.querySelectorAll('.num-btn').forEach(btn => btn.classList.remove('current'));
        document.getElementById(`grid-${currentNo}`).classList.add('current');
        
        if (data.status_map) {
            Object.keys(data.status_map).forEach(btnNo => {
                const btn = document.getElementById(`grid-${btnNo}`);
                if (btn) {
                    btn.classList.remove('answered', 'flagged');
                    if (data.status_map[btnNo].flagged) btn.classList.add('flagged');
                    else if (data.status_map[btnNo].answered) btn.classList.add('answered');
                }
            });
        }
        window.scrollTo(0, 0);
    }

    function toggleFlag() {
        const valEl = document.getElementById('is_flagged_val');
        const isCurrentlyFlagged = valEl.value === '1';
        const newFlagState = !isCurrentlyFlagged;
        valEl.value = newFlagState ? '1' : '0';
        
        const btn = document.getElementById('flagBtn');
        btn.classList.toggle('flagged', newFlagState);
        
        // Optimistic UI update pada kotak nomor di daftar soal
        const gridBtn = document.getElementById(`grid-${currentNo}`);
        if (gridBtn) {
            gridBtn.classList.remove('flagged');
            if (newFlagState) {
                gridBtn.classList.add('flagged');
            } else {
                const hasAnswer = document.querySelector('input[type="radio"][name="answer"]:checked') !== null ||
                                  document.querySelectorAll('input[type="checkbox"][name="answer[]"]:checked').length > 0 ||
                                  (document.getElementById('essayAnswer') && document.getElementById('essayAnswer').value.trim() !== '') ||
                                  gridBtn.classList.contains('answered');
                if (hasAnswer) {
                    gridBtn.classList.add('answered');
                }
            }
        }

        clearTimeout(saveTimeout);
        doSaveAnswer(true);
    }

    let saveTimeout = null;
    let lastSavedForQuestion = {};

    function saveAnswer() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(() => doSaveAnswer(false), 200);
    }

    function doSaveAnswer(force = false) {
        const qid = document.getElementById('current_question_id').value;
        const isFlagged = document.getElementById('is_flagged_val').value;
        let answer = null;
        
        const pgInput = document.querySelector('input[type="radio"][name="answer"]:checked');
        const pgkInputs = document.querySelectorAll('input[type="checkbox"][name="answer[]"]:checked');
        const mjDropzones = document.querySelectorAll('.mj-dropzone');
        const mtSelects = document.querySelectorAll('.mt-select');
        const essayInput = document.getElementById('essayAnswer');

        if (pgInput) {
            answer = pgInput.value;
        } else if (pgkInputs.length > 0) {
            answer = Array.from(pgkInputs).map(cb => cb.value);
        } else if (mjDropzones.length > 0) {
            answer = {};
            let hasAns = false;
            mjDropzones.forEach(zone => {
                if (zone.children.length > 0) {
                    let item = zone.querySelector('.mj-item');
                    if (item) {
                        answer[zone.getAttribute('data-idx')] = item.getAttribute('data-val');
                        hasAns = true;
                    }
                }
            });
            if(!hasAns) answer = null;
        } else if (mtSelects.length > 0) {
            answer = [];
            let hasAns = false;
            mtSelects.forEach(sel => { 
                answer.push(sel.value);
                if(sel.value) hasAns = true;
            });
            if(!hasAns) answer = null;
        } else if (essayInput) {
            answer = essayInput.value;
        }

        const currentPayload = JSON.stringify({ answer: answer, flagged: isFlagged });
        if (!force && lastSavedForQuestion[qid] === currentPayload) {
            return; // Benar-benar tidak ada perubahan baik jawaban maupun status ragu-ragu
        }

        isSaving = true;
        fetch(`{{ url('siswa/exam') }}/${scheduleId}/answer`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                question_id: qid,
                answer: answer,
                is_flagged: isFlagged == '1'
            })
        })
        .then(res => res.json())
        .then(data => {
            lastSavedForQuestion[qid] = currentPayload;
            pendingSavePayload = null;
            const gridBtn = document.getElementById(`grid-${currentNo}`);
            if (gridBtn) {
                gridBtn.classList.remove('answered', 'flagged');
                if (data.flagged) gridBtn.classList.add('flagged');
                else if (data.answered) gridBtn.classList.add('answered');
            }
        })
        .catch(err => {
            console.error("Save error:", err);
            pendingSavePayload = { qid, answer, isFlagged };
            if (!navigator.onLine || (err.message && err.message.includes('Failed to fetch')) || err.name === 'TypeError') {
                showOfflineModal();
            }
        })
        .finally(() => isSaving = false);
    }

    function showLoading(show) {
        document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
    }

    function confirmFinish() {
        if (saveTimeout) {
            clearTimeout(saveTimeout);
            doSaveAnswer();
        }
        if (isSaving) {
            setTimeout(confirmFinish, 300);
            return;
        }
        
        const answered = document.querySelectorAll('.num-btn.answered').length;
        const flagged = document.querySelectorAll('.num-btn.flagged').length;
        const total = totalSoal;
        
        const summary = document.getElementById('finishSummary');
        summary.innerHTML = `
            <div class="d-flex justify-content-between mb-2"><span>Total Soal:</span> <b>${total}</b></div>
            <div class="d-flex justify-content-between mb-2"><span>Sudah Dijawab:</span> <b style="color: #1e40af;">${answered}</b></div>
            <div class="d-flex justify-content-between mb-2"><span>Ragu-ragu:</span> <b class="text-warning">${flagged}</b></div>
            <div class="d-flex justify-content-between"><span>Belum Dijawab:</span> <b class="text-danger">${total - answered - flagged}</b></div>
        `;
        document.getElementById('finishModal').classList.add('show');
    }

    let isSubmittingFinish = false;
    function handleFinishSubmit(btn) {
        if (isSubmittingFinish) return;
        isSubmittingFinish = true;

        if (typeof cheatDetectorActive !== 'undefined') {
            cheatDetectorActive = false;
        }

        btn.disabled = true;
        btn.style.opacity = '0.7';
        btn.style.cursor = 'not-allowed';
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengirim...';
        
        const cancelBtn = document.getElementById('btnCancelFinish');
        if (cancelBtn) cancelBtn.style.display = 'none';

        document.getElementById('finishForm').submit();
    }

    // ── Keamanan Global (Selalu Aktif) ──
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
    document.addEventListener('cut', e => e.preventDefault());
    document.addEventListener('paste', e => e.preventDefault());
    document.addEventListener('selectstart', e => e.preventDefault());
    document.addEventListener('dragstart', e => e.preventDefault());
    document.addEventListener('drop', e => e.preventDefault());

    // Layar Tetap Menyala (Screen Wake Lock API)
    if ('wakeLock' in navigator) {
        navigator.wakeLock.request('screen').catch(() => {});
    }

    // ── Anti-Cheat Logic ──
    @if($schedule->enforce_focus)
    let cheatDetectorActive = true;
    
    function kickOut(reason) {
        if (!cheatDetectorActive) return;
        cheatDetectorActive = false;

        document.body.classList.add('screen-protected');
        const form = document.getElementById('antiCheatForm');
        if (form) {
            const inputReason = document.getElementById('antiCheatReason');
            if (inputReason) {
                inputReason.value = reason || 'Membuka tab baru atau aplikasi lain';
            }
            try {
                const formData = new FormData(form);
                if (navigator.sendBeacon) {
                    navigator.sendBeacon(form.action, formData);
                } else {
                    fetch(form.action, { method: 'POST', body: formData, keepalive: true }).catch(() => {});
                }
            } catch (err) {}

            form.submit();
        }
    }

    // 1. Deteksi Pindah Tab / Minimalisir Aplikasi
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden' && cheatDetectorActive) {
            document.body.classList.add('screen-protected');
            kickOut('Deteksi Kecurangan: Anda berpindah tab atau meminimalkan aplikasi!');
        } else {
            document.body.classList.remove('screen-protected');
        }
    });

    // 2. Deteksi Kehilangan Fokus Window (Tarik status bar / notifikasi / klik luar jendela ujian)
    window.addEventListener('blur', () => {
        document.body.classList.add('screen-protected');
        if (cheatDetectorActive) {
            setTimeout(() => {
                if (document.hidden || !document.hasFocus()) {
                    kickOut('Deteksi Kecurangan: Anda membuka jendela lain atau meninggalkan layar ujian!');
                }
            }, 250);
        }
    });

    window.addEventListener('focus', () => {
        document.body.classList.remove('screen-protected');
    });

    // 3. Deteksi Split Screen (Layar Terbelah) di Mobile & Desktop (Windows Snap / Resize Jendela)
    window.addEventListener('resize', () => {
        if (!cheatDetectorActive) return;
        const activeTag = document.activeElement ? document.activeElement.tagName : '';
        const isEditing = activeTag === 'INPUT' || activeTag === 'TEXTAREA';
        if (isEditing) return;

        // Cek Split Screen Vertikal (Mobile) & Horizontal (Desktop Windows Snap / Resize ke samping)
        const isHeightSplit = window.innerHeight < (window.screen.availHeight * 0.65);
        const isWidthSplit = window.innerWidth < (window.screen.availWidth * 0.70);

        if (isHeightSplit || isWidthSplit) {
            document.body.classList.add('screen-protected');
            kickOut('Deteksi Kecurangan: Fitur Split Screen / Layar Terbelah atau memperkecil jendela tidak diizinkan!');
        }
    });

    // 4. Blokir Gesture 3 Jari & Multi-touch (Mencegah Screenshot Gestur Bawaan HP)
    document.addEventListener('touchstart', (e) => {
        if (e.touches.length >= 2) {
            e.preventDefault();
        }
        // Cegah tarik dari status bar paling atas (40px teratas)
        if (e.touches[0].clientY < 40) {
            e.preventDefault();
        }
    }, { passive: false });

    document.addEventListener('touchmove', (e) => {
        if (e.touches.length >= 2) {
            e.preventDefault();
        }
    }, { passive: false });

    // 5. Blokir Tombol Pintasan Screenshot Keyboard & Developer Tools
    window.addEventListener('keyup', (e) => {
        if (e.key === 'PrintScreen' || e.keyCode === 44) {
            if (navigator.clipboard) navigator.clipboard.writeText('');
            document.body.classList.add('screen-protected');
            setTimeout(() => document.body.classList.remove('screen-protected'), 1500);
            if (cheatDetectorActive) kickOut('Deteksi Kecurangan: Tangkapan Layar (Screenshot / PrintScreen) terdeteksi!');
        }
    });

    document.addEventListener('keydown', function(e) {
        // Tombol F1 - F12 (F12 DevTools, F5 reload, F11 fullscreen, dll)
        if (e.keyCode >= 112 && e.keyCode <= 123) {
            e.preventDefault();
            return false;
        }

        // Tombol PrintScreen (Screenshot)
        if (e.key === 'PrintScreen' || e.keyCode === 44) {
            e.preventDefault();
            if (navigator.clipboard) navigator.clipboard.writeText('');
            kickOut('Deteksi Kecurangan: Tangkapan Layar (Screenshot / PrintScreen) terdeteksi!');
            return false;
        }

        // Tombol Alt (Mencegah Alt+Tab, Alt+Panah Balik, Alt menu)
        if (e.altKey) {
            e.preventDefault();
            return false;
        }

        // Tombol Windows / Meta (Mencegah Windows Snap / Win+Shift+S Snipping Tool)
        if (e.metaKey) {
            e.preventDefault();
            if (e.shiftKey) {
                kickOut('Deteksi Kecurangan: Shortcut Tangkapan Layar (Snipping Tool) terdeteksi!');
            }
            return false;
        }

        // Tombol Ctrl kombinasi (Blokir copy, paste, select all, new tab/window, source, print, search, dll)
        if (e.ctrlKey) {
            const blockedKeys = ['c', 'v', 'x', 'a', 'u', 's', 'p', 't', 'n', 'w', 'h', 'j', 'r', 'f', 'g', 'k', 'l', 'o', 'e', 'b', 'i', 'q'];
            if (blockedKeys.includes(e.key.toLowerCase())) {
                e.preventDefault();
                return false;
            }
        }
    }, true);

    // 6. Peringatan konfirmasi jika reload / tutup tab browser
    window.addEventListener('beforeunload', function(e) {
        if (cheatDetectorActive) {
            e.preventDefault();
            e.returnValue = 'Apakah Anda yakin ingin meninggalkan ujian? Tindakan ini akan dicatat sebagai pelanggaran.';
            return e.returnValue;
        }
    });
    @endif

    function confirmLogout() {
        Swal.fire({
            title: 'Yakin ingin keluar?',
            text: 'Jawaban sementara telah tersimpan otomatis.',
            icon: 'warning',
            width: '300px',
            padding: '1em',
            showCancelButton: true,
            confirmButtonText: 'Ya, Keluar',
            cancelButtonText: 'Batal',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-4 pb-3',
                title: 'fs-6 mt-1 mb-1',
                htmlContainer: 'small text-muted mb-2',
                actions: 'mt-0',
                confirmButton: 'btn btn-primary btn-sm px-3 mx-1',
                cancelButton: 'btn btn-danger btn-sm px-3 mx-1'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (typeof cheatDetectorActive !== 'undefined') {
                    cheatDetectorActive = false;
                }
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>

<form action="{{ route('siswa.exam.block', $schedule->id) }}" method="POST" id="antiCheatForm" style="display:none;">
    @csrf
    <input type="hidden" name="reason" id="antiCheatReason" value="">
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const btnFullscreen = document.getElementById('btn-fullscreen');
    const docElm = document.documentElement;
    const isFullscreenSupported = docElm.requestFullscreen || docElm.webkitRequestFullscreen || docElm.msRequestFullscreen;

    let hasEnteredFullscreen = false;

    if (!isFullscreenSupported) {
        document.body.classList.add('is-fullscreen');
    } else {
        btnFullscreen.addEventListener('click', function() {
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            } else if (docElm.webkitRequestFullscreen) {
                docElm.webkitRequestFullscreen();
            } else if (docElm.msRequestFullscreen) {
                docElm.msRequestFullscreen();
            }
        });
        
        function onFullScreenChange() {
            let isFullScreen = document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
            let isPwaMode = window.matchMedia('(display-mode: standalone)').matches 
                || window.matchMedia('(display-mode: fullscreen)').matches 
                || window.navigator.standalone === true;
            if (isFullScreen || isPwaMode) {
                document.body.classList.add('is-fullscreen');
                hasEnteredFullscreen = true;
            } else {
                document.body.classList.remove('is-fullscreen');
                @if($schedule->enforce_focus)
                if (hasEnteredFullscreen && typeof cheatDetectorActive !== 'undefined' && cheatDetectorActive) {
                    const activeTag = document.activeElement ? document.activeElement.tagName : '';
                    const isEditing = activeTag === 'INPUT' || activeTag === 'TEXTAREA';
                    if (!isEditing) {
                        kickOut('Deteksi Kecurangan: Anda keluar dari mode Layar Penuh (Fullscreen)!');
                    }
                }
                @endif
            }
        }
        
        document.addEventListener("fullscreenchange", onFullScreenChange);
        document.addEventListener("webkitfullscreenchange", onFullScreenChange);
        document.addEventListener("msfullscreenchange", onFullScreenChange);
        onFullScreenChange();

        // Minta layar penuh otomatis pada sentuhan pertama jika belum penuh
        document.addEventListener('touchstart', function autoFs() {
            let isFs = document.fullscreenElement || document.webkitFullscreenElement || document.msFullscreenElement;
            if (!isFs && docElm.requestFullscreen) {
                docElm.requestFullscreen().catch(() => {});
            }
        }, { once: true });
    }

    // Hubungkan tombol coba lagi koneksi manual
    const btnRetry = document.getElementById('btn-retry-connection');
    if (btnRetry) {
        btnRetry.addEventListener('click', function() {
            btnRetry.disabled = true;
            btnRetry.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menguji Koneksi...';
            
            fetch(`{{ url('ping') }}?_t=${Date.now()}`, {
                method: 'GET',
                cache: 'no-store',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (res.ok) {
                    hideOfflineModal();
                } else {
                    throw new Error('Server belum merespon');
                }
            })
            .catch(() => {
                const statusText = document.getElementById('offline-status-text');
                if (statusText) statusText.textContent = 'Koneksi belum terhubung. Mencoba lagi...';
            })
            .finally(() => {
                btnRetry.disabled = false;
                btnRetry.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg> Coba Hubungkan Sekarang`;
            });
        });
    }

    if (!navigator.onLine) {
        showOfflineModal();
    }

    // Auto-reload jika halaman dimuat dari cache tombol 'Kembali' (BFCache browser)
    window.addEventListener('pageshow', function (event) {
        if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
});
</script>
</body>
</html>




