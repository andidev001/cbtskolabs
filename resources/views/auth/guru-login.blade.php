<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sign In Admin | SKOLABS CBT</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f8fbff;
            overflow-x: hidden;
        }

        /* Abstract Background Elements */
        .bg-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        /* The blue shapes on the left */
        .shape-light-blue {
            position: absolute;
            width: 500px;
            height: 600px;
            background: linear-gradient(135deg, #71b7ff 0%, #469cf5 100%);
            border-radius: 80px;
            transform: rotate(-35deg);
            top: 25%;
            left: -200px;
            opacity: 0.9;
        }
        
        .shape-dark-blue {
            position: absolute;
            width: 600px;
            height: 600px;
            background: linear-gradient(135deg, #1f67f2 0%, #154cbd 100%);
            border-radius: 100px;
            transform: rotate(-35deg);
            top: 60%;
            left: -150px;
            opacity: 0.95;
        }

        /* Faint network pattern */
        .network-pattern {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(#c2d4f2 2px, transparent 2px), 
                radial-gradient(#c2d4f2 2px, transparent 2px);
            background-size: 60px 60px;
            background-position: 0 0, 30px 30px;
            opacity: 0.5;
            mask-image: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 60%);
            -webkit-mask-image: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 60%);
        }

        .main-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .left-content {
            padding: 4rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .logo-container img {
            width: 70px;
            height: auto;
        }

        .logo-text {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .welcome-text {
            font-size: 2.2rem;
            font-weight: 500;
            color: #111;
            line-height: 1.3;
        }

        .login-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.04);
            width: 100%;
            max-width: 380px;
            margin: 0 auto;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .login-card-title {
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: #3f4254;
            margin-bottom: 8px;
        }

        .login-card-subtitle {
            text-align: center;
            font-size: 0.9rem;
            color: #7e8299;
            margin-bottom: 2.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #3f4254;
            margin-bottom: 8px;
        }

        .form-control {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border: 1px solid #e4e6ef;
            border-radius: 8px;
            background-color: #ffffff;
            color: #3f4254;
        }

        .form-control::placeholder {
            color: #b5b5c3;
        }

        .form-control:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 0.2rem rgba(0, 158, 247, 0.1);
        }

        .input-group-text {
            background-color: transparent;
            border: 1px solid #e4e6ef;
            border-radius: 0 8px 8px 0;
            border-left: none;
            color: #a1a5b7;
        }
        
        .form-control-password {
            border-right: none;
            border-radius: 8px 0 0 8px;
        }

        .btn-primary {
            background-color: #1e40af;
            border-color: #1e40af;
            padding: 0.8rem;
            font-weight: 600;
            border-radius: 8px;
            font-size: 1rem;
            width: 100%;
            margin-top: 1rem;
            box-shadow: 0 4px 10px rgba(30, 64, 175, 0.2);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1e3a8a;
            border-color: #1e3a8a;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(30, 64, 175, 0.3);
        }

        @media (max-width: 991px) {
            .left-content {
                text-align: center;
                margin-bottom: 0.5rem;
                padding: 1.5rem 1rem 0.5rem 1rem;
            }
            .logo-container {
                justify-content: center;
                margin-bottom: 10px;
            }
            .logo-text {
                font-size: 1.6rem;
            }
            .welcome-text {
                font-size: 1.5rem;
            }
            .login-card {
                padding: 1.8rem 1.5rem;
                margin-bottom: 2rem;
                max-width: 360px;
            }
            .shape-light-blue, .shape-dark-blue {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="bg-shapes">
        <div class="network-pattern"></div>
        <div class="shape-light-blue"></div>
        <div class="shape-dark-blue"></div>
    </div>

    <div class="container main-container">
        <div class="row w-100 align-items-center">
            
            <!-- Left Content -->
            <div class="col-lg-6 left-content">
                <div class="logo-container">
                    @php
                        $app_logo = \App\Models\Setting::get('app_logo');
                        $app_name = \App\Models\Setting::get('app_name', 'SKOLABS CBT');
                    @endphp
                    @if($app_logo)
                        <img src="{{ Storage::url($app_logo) }}" alt="Logo">
                    @else
                        <img src="{{ asset('logoskolabs.png') }}" alt="Logo" style="height: 60px; object-fit: contain;">
                    @endif
                    <h1 class="logo-text">{{ $app_name }}</h1>
                </div>
                <h2 class="welcome-text">Selamat Datang Di<br>CBT Guru</h2>
            </div>

            <!-- Right Content -->
            <div class="col-lg-6">
                <div class="login-card">
                    <h3 class="login-card-title">Sign In</h3>
                    <p class="login-card-subtitle">Masukan Username & password anda!</p>

                    <form action="{{ route('guru.login.submit') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label">Username</label>
                            <input class="form-control @error('username') is-invalid @enderror" type="text" name="username" placeholder="Masukkan Username" value="{{ old('username') }}" required autofocus />
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input class="form-control form-control-password @error('password') is-invalid @enderror" type="password" name="password" id="password" placeholder="Masukkan password" required />
                                <span class="input-group-text bg-white" id="togglePassword" style="cursor: pointer;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Tenant as requested in the mockup (using it as an inert field or just keeping remember me) -->
                        <!-- Since user says "ikuti data aplikasi", I'll keep Remember me as it's useful, or hide it. I will keep it small. -->
                        <div class="d-flex align-items-center mb-4">
                            <div class="form-check">
                                <input id="customControlInline" type="checkbox" class="form-check-input" value="remember-me" name="remember-me" checked>
                                <label class="form-check-label" for="customControlInline" style="font-size: 0.85rem; color: #7e8299;">Remember me</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Sign-In</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            if (type === 'text') {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye-off"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        });
    </script>
</body>
</html>
