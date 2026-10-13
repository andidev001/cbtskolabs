<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Siswa Login | SKOLABS CBT</title>
	@php
		$app_logo = \App\Models\Setting::get('app_logo');
		$favicon = $app_logo ? \Storage::url($app_logo) : asset('logoskolabs.png');
	@endphp
	<link rel="shortcut icon" href="{{ $favicon }}" />
	<link rel="manifest" href="{{ asset('manifest.json') }}">
	<meta name="theme-color" content="#1e40af">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
	<meta name="apple-mobile-web-app-title" content="CBT Siswa">
	<link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #f4f6f9; 
            font-family: 'Inter', sans-serif; 
            margin: 0; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: #333;
        }

        /* ── Header Area ── */
        .header-bg {
            background: #1e40af; /* Professional deep blue */
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            position: relative;
            padding: 40px 20px 80px;
            text-align: center;
        }
        


        .school-info {
            position: relative;
            z-index: 2;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .school-info img {
            width: 55px;
            height: 55px;
            object-fit: cover;
            border-radius: 50%;
            background: #fff;
            padding: 3px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .school-text {
            text-align: left;
        }
        .school-text h1 {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .school-text p {
            margin: 0;
            font-size: 0.75rem;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Login Card ── */
        .login-wrapper {
            flex: 1;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 0 15px;
            margin-top: -30px;
            position: relative;
            z-index: 10;
        }
        .login-card {
            background: #fff;
            width: 100%;
            max-width: 350px; /* Smaller, more compact */
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.04), 0 5px 15px rgba(0,0,0,0.03);
            padding: 30px 25px;
        }
        
        .help-text {
            font-size: 0.7rem;
            color: #6c757d;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .help-text b { color: #1e40af; font-weight: 700; }
        
        .welcome-title {
            font-size: 1.5rem;
            color: #2b3440;
            margin: 0 0 8px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }
        .welcome-sub {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        /* ── Form Inputs ── */
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        .form-icon {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            width: 18px;
            height: 18px;
            transition: color 0.2s;
        }
        .form-control {
            width: 100%;
            border: none;
            border-bottom: 1.5px solid #e9ecef;
            padding: 10px 30px;
            font-size: 0.9rem;
            color: #333;
            background: transparent;
            box-sizing: border-box;
            transition: all 0.2s;
            border-radius: 0;
        }
        .form-control:focus {
            outline: none;
            border-bottom-color: #1e40af;
        }
        .form-control:focus + .form-icon,
        .form-control:focus ~ .form-icon {
            color: #1e40af; /* Highlight icon on focus */
        }
        .form-control::placeholder {
            color: #adb5bd;
            font-weight: 400;
        }
        
        .password-toggle {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
            cursor: pointer;
            padding: 5px;
            transition: color 0.2s;
        }
        .password-toggle:hover { color: #6c757d; }
        
        .invalid-feedback {
            color: #e3342f;
            font-size: 0.75rem;
            margin-top: 5px;
            display: block;
        }

        /* ── Action Button ── */
        .btn-login {
            background: #1e40af;
            color: #fff;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(26, 54, 93, 0.2);
        }
        .btn-login:hover { 
            background: #1e3a8a; 
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(26, 54, 93, 0.3);
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            font-size: 0.7rem;
            color: #adb5bd;
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #f1f3f5;
        }
        
        @media (max-width: 576px) {
            .header-bg { padding: 30px 15px 70px; }
            .login-card { padding: 25px 20px; }
            .welcome-title { font-size: 1.35rem; }
        }

        /* ── PWA Install Banner & Modal ── */
        .pwa-banner {
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
            border: 1.5px solid #bfdbfe;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 22px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.06);
            transition: all 0.3s ease;
        }
        .pwa-banner-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .pwa-banner-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e3a8a;
        }
        .pwa-banner-desc {
            font-size: 0.72rem;
            color: #475569;
            margin: 0 0 10px;
            line-height: 1.35;
        }
        .btn-install-pwa {
            background: #1e40af;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.25);
            transition: all 0.2s ease;
        }
        .btn-install-pwa:hover {
            background: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30, 64, 175, 0.35);
        }
        .pwa-active-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            margin-bottom: 18px;
        }
        
        /* Modal Guide */
        .pwa-modal-backdrop {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(4px);
        }
        .pwa-modal-card {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            text-align: left;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }
        .pwa-modal-close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: #f1f5f9;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #64748b;
        }
        .pwa-modal-close:hover { background: #e2e8f0; color: #1e293b; }
        .pwa-step-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }
        .pwa-step-num {
            background: #1e40af;
            color: #fff;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .pwa-step-text {
            font-size: 0.8rem;
            color: #334155;
            line-height: 1.45;
        }
    </style>
</head>
<body>

    @php
        $app_logo = \App\Models\Setting::get('app_logo');
        $app_name = \App\Models\Setting::get('app_name', 'SKOLABS CBT');
    @endphp

    <div class="header-bg">
        <div class="school-info">
            @if($app_logo)
                <img src="{{ Storage::url($app_logo) }}" alt="Logo">
            @else
                <img src="{{ asset('logoskolabs.png') }}" alt="Logo" style="height: 55px; object-fit: contain; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #fff; padding: 2px;">
            @endif
            <div class="school-text">
                <h1>{{ $app_name }}</h1>
                <p>Computer Based Test</p>
            </div>
        </div>
        

    </div>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="help-text">
                Lupa Username / Password? Hubungi <b>Admin Sekolah</b>
            </div>

            <h2 class="welcome-title">Selamat Datang</h2>
            <p class="welcome-sub">Silakan login menggunakan username dan password yang Anda miliki</p>

            {{-- PWA ExamBrowser Status / Install Banner --}}
            <div id="pwa-installed-badge" style="display: none; text-align: center;">
                <span class="pwa-active-badge">
                    <svg style="width: 15px; height: 15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Mode ExamBrowser Aktif
                </span>
            </div>

            <div id="pwa-install-container" class="pwa-banner">
                <div class="pwa-banner-header">
                    <svg style="width: 18px; height: 18px; color: #1e40af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span class="pwa-banner-title">Aplikasi ExamBrowser CBT</span>
                </div>
                <p class="pwa-banner-desc">
                    Kunci layar ujian penuh, tanpa tab baru & tanpa split screen di HP.
                </p>
                <button type="button" id="btn-install-pwa" class="btn-install-pwa">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span>Install Aplikasi (APK)</span>
                </button>
            </div>

            <form action="{{ route('siswa.login.submit') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <input type="text" class="form-control @error('username') is-invalid @enderror" name="username" placeholder="Username / NISN" value="{{ old('username') }}" required autofocus>
                    <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    @error('username') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" id="password" placeholder="Password" required>
                    <svg class="form-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                    
                    <span class="password-toggle" id="togglePassword">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="footer">
                &copy; {{ date('Y') }} {{ $app_name }} &mdash; CBT Application
            </div>
        </div>
    </div>

    {{-- Modal Petunjuk Install Manual (iOS & Android Fallback) --}}
    <div id="pwa-modal-guide" class="pwa-modal-backdrop">
        <div class="pwa-modal-card">
            <button type="button" class="pwa-modal-close" onclick="closePwaModal()">&times;</button>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                <div style="background: #eff6ff; padding: 10px; border-radius: 12px; color: #1e40af;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <h5 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e293b;">Petunjuk Pasang Aplikasi</h5>
                    <small style="color: #64748b; font-size: 0.75rem;">Jalankan mode ExamBrowser di perangkat Anda</small>
                </div>
            </div>

            <div id="pwa-guide-ios" style="display: none;">
                <p style="font-size: 0.8rem; color: #475569; margin-bottom: 12px;">Untuk pengguna <strong>iPhone / iPad (Safari)</strong>:</p>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">1</div>
                    <div class="pwa-step-text">Buka halaman ini menggunakan browser <strong>Safari</strong> bawaan iPhone.</div>
                </div>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">2</div>
                    <div class="pwa-step-text">Ketuk tombol <strong>Bagikan (Share)</strong> di bagian bawah layar (ikon kotak dengan panah ke atas).</div>
                </div>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">3</div>
                    <div class="pwa-step-text">Gulir ke bawah lalu pilih menu <strong>"Tambah ke Layar Utama" (Add to Home Screen)</strong>.</div>
                </div>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">4</div>
                    <div class="pwa-step-text">Buka aplikasi dari layar utama HP untuk masuk ke mode ujian bebas gangguan.</div>
                </div>
            </div>

            <div id="pwa-guide-android">
                <p style="font-size: 0.8rem; color: #475569; margin-bottom: 12px;">Untuk pengguna <strong>Android (Chrome)</strong>:</p>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">1</div>
                    <div class="pwa-step-text">Buka halaman ini di browser <strong>Google Chrome</strong>.</div>
                </div>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">2</div>
                    <div class="pwa-step-text">Ketuk ikon <strong>titik tiga (⋮)</strong> di sudut kanan atas browser.</div>
                </div>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">3</div>
                    <div class="pwa-step-text">Pilih menu <strong>"Instal aplikasi"</strong> atau <strong>"Tambahkan ke Layar Utama"</strong>.</div>
                </div>
                <div class="pwa-step-item">
                    <div class="pwa-step-num">4</div>
                    <div class="pwa-step-text">Buka aplikasi dari layar beranda HP Anda untuk memulai ujian.</div>
                </div>
            </div>

            <button type="button" onclick="closePwaModal()" style="width: 100%; margin-top: 15px; background: #1e40af; color: #fff; border: none; border-radius: 50px; padding: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                Mengerti
            </button>
        </div>
    </div>

    <script>
        // Password toggle
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            if (type === 'text') {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        });

        // ── PWA Logic ──
        let deferredPrompt = null;
        const installBtn = document.getElementById('btn-install-pwa');
        const installContainer = document.getElementById('pwa-install-container');
        const activeBadge = document.getElementById('pwa-installed-badge');
        const modalGuide = document.getElementById('pwa-modal-guide');
        const guideIos = document.getElementById('pwa-guide-ios');
        const guideAndroid = document.getElementById('pwa-guide-android');

        // Check if currently opened in standalone PWA mode
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches 
            || window.navigator.standalone === true 
            || document.referrer.includes('android-app://');

        if (isStandalone) {
            installContainer.style.display = 'none';
            activeBadge.style.display = 'block';
        } else {
            installContainer.style.display = 'block';
            activeBadge.style.display = 'none';
        }

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(() => {});
            });
        }

        // Catch Android Chrome install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installContainer.style.display = 'block';
            activeBadge.style.display = 'none';
        });

        // App Installed event
        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            installContainer.style.display = 'none';
            activeBadge.style.display = 'block';
        });

        // Handle Install Button Click
        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                if (outcome === 'accepted') {
                    installContainer.style.display = 'none';
                    activeBadge.style.display = 'block';
                }
                deferredPrompt = null;
            } else {
                // If native prompt is not available (e.g. iOS Safari or browser already prompt-blocked)
                const isIos = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
                if (isIos) {
                    guideIos.style.display = 'block';
                    guideAndroid.style.display = 'none';
                } else {
                    guideIos.style.display = 'none';
                    guideAndroid.style.display = 'block';
                }
                modalGuide.style.display = 'flex';
            }
        });

        function closePwaModal() {
            modalGuide.style.display = 'none';
        }

        // Close modal when clicking backdrop
        modalGuide.addEventListener('click', function(e) {
            if (e.target === modalGuide) {
                closePwaModal();
            }
        });
    </script>
</body>
</html>
