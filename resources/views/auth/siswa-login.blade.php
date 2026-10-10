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

    <script>
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
    </script>
</body>
</html>
