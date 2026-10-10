<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	@php
		$app_logo = \App\Models\Setting::get('app_logo');
		$favicon = $app_logo ? \Storage::url($app_logo) : asset('logoskolabs.png');
	@endphp
	<link rel="shortcut icon" href="{{ $favicon }}" />
	<title>@yield('title') | SKOLABS CBT — Guru</title>
	<link href="{{ asset('assets/adminkit/css/app.css') }}" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --sidebar-bg: #0d3a85;       /* Biru tua – sama dengan header siswa */
            --sidebar-accent: #60a5fa;   /* Biru cerah untuk aksen aktif */
            --sidebar-link-color: #bfdbfe;
            --sidebar-link-hover: #ffffff;
            --sidebar-header: #93c5fd;
        }

        .sidebar, .sidebar-content { background: var(--sidebar-bg) !important; }

        .sidebar-brand {
            background: rgba(0,0,0,0.1);
            padding: 1.5rem 1.5rem !important;
            font-weight: 800 !important;
            letter-spacing: -0.5px;
            color: #fff !important;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .sidebar-brand .role-badge {
            display: inline-block;
            background: var(--sidebar-accent);
            color: #fff;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 6px;
            vertical-align: middle;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .sidebar-header {
            color: var(--sidebar-header) !important;
            padding: 1.5rem 1.5rem 0.5rem !important;
            font-size: 0.65rem !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.7;
        }

        .sidebar-item.active .sidebar-link {
            background: linear-gradient(90deg, rgba(96,165,250,0.2) 0%, rgba(96,165,250,0) 100%) !important;
            color: var(--sidebar-accent) !important;
            border-left: 3px solid var(--sidebar-accent);
        }
        .sidebar-item.active i, .sidebar-item.active span { color: var(--sidebar-accent) !important; }
        
        .sidebar-dropdown .sidebar-item.active .sidebar-link {
            background: #ffffff !important;
            color: var(--sidebar-bg) !important;
            border-radius: 6px;
            margin: 4px 1.5rem;
            padding: 0.5rem 1rem !important;
            border-left: none !important;
        }
        .sidebar-dropdown .sidebar-item.active i, 
        .sidebar-dropdown .sidebar-item.active span { 
            color: var(--sidebar-bg) !important; 
        }

        .sidebar-link {
            background: transparent !important;
            color: var(--sidebar-link-color) !important;
            padding: 0.75rem 1.5rem !important;
            transition: all 0.2s ease;
            font-weight: 500 !important;
            border-left: 3px solid transparent;
            position: relative;
        }
        .sidebar-link[data-bs-toggle="collapse"]::after {
            border: solid;
            border-width: 0 .075rem .075rem 0;
            content: " ";
            display: inline-block;
            padding: 2px;
            position: absolute;
            right: 1.5rem;
            top: 1.2rem;
            transform: rotate(45deg);
            transition: all .2s ease-out;
        }
        .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
            transform: rotate(-45deg);
        }
        .sidebar-link:hover {
            color: var(--sidebar-link-hover) !important;
            background: rgba(255,255,255,0.04) !important;
        }
        .sidebar-link i { width: 18px; height: 18px; margin-right: 12px; opacity: 0.7; }
        .sidebar-nav { padding-bottom: 2rem; }
        .sidebar-content::-webkit-scrollbar { width: 4px; }
        .sidebar-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        /* Guru profile chip in sidebar */
        .guru-chip {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 18px; margin: 12px 12px 0;
            background: rgba(255,255,255,0.06);
            border-radius: 12px; border: 1px solid rgba(255,255,255,0.08);
        }
        .guru-chip .avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--sidebar-accent);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.9rem; color: #fff; flex-shrink: 0;
        }
        .guru-chip .info .name { font-size: 0.82rem; color: #fff; font-weight: 600; }
        .guru-chip .info .role { font-size: 0.7rem; color: var(--sidebar-link-color); }
    </style>

    
    <style>
        /* custom fine ui */
        body { font-size: 0.875rem; background-color: #f5f7fb; }
        .card { box-shadow: 0 0 10px rgba(0,0,0,0.03) !important; border: none !important; border-radius: 8px !important; margin-bottom: 1.5rem; }
        .card-header { border-bottom: 1px solid #f0f2f5; background-color: #fff; border-radius: 8px 8px 0 0 !important; padding: 1rem 1.25rem; }
        .card-body { padding: 1.25rem; }
        .form-control, .form-select, .btn { font-size: 0.85rem !important; border-radius: 5px !important; }
        
        /* Kekinian Table & DataTables */
        .table { font-size: 0.8rem !important; color: #4a5568 !important; border-collapse: separate !important; border-spacing: 0 !important; }
        .table > :not(caption) > * > * { padding: 0.7rem 1rem !important; vertical-align: middle !important; border-bottom: 1px dashed #e2e8f0 !important; }
        .table thead th { font-weight: 600 !important; font-size: 0.7rem !important; text-transform: uppercase !important; letter-spacing: 0.8px !important; background-color: #f8fafc !important; color: #64748b !important; border-bottom: none !important; border-top: none !important; padding: 0.85rem 1rem !important; }
        .table thead tr th:first-child { border-top-left-radius: 8px !important; border-bottom-left-radius: 8px !important; }
        .table thead tr th:last-child { border-top-right-radius: 8px !important; border-bottom-right-radius: 8px !important; }
        .table tbody tr:last-child td { border-bottom: none !important; }
        .table tbody tr { transition: all 0.2s ease !important; }
        .table tbody tr:hover { background-color: #f1f5f9 !important; transform: scale(1.001) !important; }
        
        /* DataTables wrapper spacing & fixes */
        .dataTables_wrapper { padding: 0.5rem 0 !important; }
        .dataTables_wrapper .row { align-items: center !important; margin-bottom: 0.75rem !important; }
        div.dataTables_wrapper div.dataTables_length label { font-weight: 500 !important; color: #64748b !important; font-size: 0.8rem !important; }
        div.dataTables_wrapper div.dataTables_length select { width: auto !important; display: inline-block !important; padding: 0.3rem 2rem 0.3rem 0.75rem !important; font-size: 0.8rem !important; border-radius: 6px !important; border: 1px solid #cbd5e1 !important; outline: none !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important; color: #475569 !important; }
        div.dataTables_wrapper div.dataTables_filter label { font-weight: 500 !important; color: #64748b !important; font-size: 0.8rem !important; }
        div.dataTables_wrapper div.dataTables_filter input { margin-left: 0.5em !important; display: inline-block !important; width: 200px !important; padding: 0.4rem 0.85rem !important; font-size: 0.8rem !important; border-radius: 20px !important; border: 1px solid #cbd5e1 !important; outline: none !important; transition: all 0.2s !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,0.05) !important; }
        div.dataTables_wrapper div.dataTables_filter input:focus { border-color: #3b82f6 !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important; width: 230px !important; }
        
        /* Modern Pagination */
        .pagination { margin-bottom: 0 !important; justify-content: flex-end !important; display: flex !important; flex-wrap: wrap !important; list-style: none !important; gap: 4px !important; padding-left: 0 !important; }
        .page-item .page-link { font-size: 0.8rem !important; font-weight: 500 !important; padding: 0.3rem 0.75rem !important; border-radius: 6px !important; margin: 0 !important; border: none !important; color: #64748b !important; background: transparent !important; transition: all 0.2s !important; }
        .page-item .page-link:hover { background-color: #f1f5f9 !important; color: #334155 !important; }
        .page-item.active .page-link { background-color: #3b82f6 !important; color: #fff !important; box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3) !important; }
        .page-item.disabled .page-link { color: #cbd5e1 !important; background: transparent !important; }

        /* Icon ONLY Buttons inside table - Small and Smooth */
        .table .btn { 
            padding: 0 !important; 
            font-size: 0 !important; /* Hide Text */
            border-radius: 6px !important; 
            box-shadow: none !important; 
            transition: all 0.2s !important; 
            display: inline-flex !important; 
            align-items: center !important; 
            justify-content: center !important; 
            margin-right: 4px !important;
            width: 28px !important;
            height: 28px !important;
        }
        .table .btn i, .table .btn svg { 
            width: 14px !important; 
            height: 14px !important; 
            font-size: 14px !important; 
            margin: 0 !important; 
        }
        .table .btn:hover { 
            transform: translateY(-1px) !important; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important; 
            background-color: rgba(0,0,0,0.05); /* Slight darkening for better feedback */
        }
        
        /* Badges */
        .badge { padding: 0.35em 0.65em; font-weight: 500; border-radius: 4px; }

        /* Fix Breadcrumbs globally */
        ol.breadcrumb { list-style: none !important; display: flex !important; flex-wrap: wrap; padding-left: 0 !important; margin-bottom: 1rem !important; gap: 0.5rem; }
        ol.breadcrumb li { display: flex; align-items: center; }
        .breadcrumb-item + .breadcrumb-item::before { content: '/'; margin-right: 0.5rem; color: #6c757d; }
        
        .sidebar-link { font-size: 0.85rem; padding: 0.65rem 1.5rem !important; }
        .sidebar-header { font-size: 0.65rem !important; padding: 1.2rem 1.5rem 0.5rem !important; }
        .navbar { padding: 0.5rem 1rem !important; min-height: 50px; border-bottom: 1px solid #f0f2f5; box-shadow: none; }

        /* SweetAlert Tweaks (Smaller & Smoother) */
        .swal2-popup { font-size: 0.85rem !important; padding: 1.5rem !important; width: auto !important; min-width: 300px !important; max-width: 380px !important; border-radius: 12px !important; }
        .swal2-title { font-size: 1.3rem !important; margin-bottom: 0.25rem !important; font-weight: 600 !important; }
        .swal2-html-container { font-size: 0.9rem !important; margin: 0.5rem 0 !important; color: #64748b !important; }
        .swal2-icon { width: 4.5rem !important; height: 4.5rem !important; margin: 1rem auto !important; }
        .swal2-icon .swal2-icon-content { font-size: 2.5rem !important; }
        .swal2-actions { margin-top: 1rem !important; gap: 8px !important; }
        .swal2-styled { padding: 0.4rem 1rem !important; font-size: 0.85rem !important; border-radius: 6px !important; font-weight: 500 !important; }
        
        /* Full Modal CSS Fallback (Adminkit missing modal styles) */
        .modal { position: fixed; top: 0; left: 0; z-index: 1055; display: none; width: 100%; height: 100%; overflow-x: hidden; overflow-y: auto; outline: 0; background: rgba(0,0,0,0.5); }
        .modal.show { display: block; }
        .modal-dialog { position: relative; width: auto; margin: 1.75rem auto; pointer-events: none; max-width: 500px; }
        .modal-dialog.modal-lg { max-width: 800px; }
        .modal-dialog.modal-sm { max-width: 300px; }
        .modal-content { position: relative; display: flex; flex-direction: column; width: 100%; pointer-events: auto; background-color: #fff; background-clip: padding-box; border: 1px solid rgba(0,0,0,0.2); border-radius: 0.5rem; outline: 0; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); }
        .modal-header { display: flex; flex-shrink: 0; align-items: center; justify-content: space-between; padding: 1rem 1rem; border-bottom: 1px solid #dee2e6; border-top-left-radius: calc(0.5rem - 1px); border-top-right-radius: calc(0.5rem - 1px); }
        .modal-title { margin-bottom: 0; line-height: 1.5; font-size: 1.25rem; font-weight: 600; color: inherit; }
        .modal-body { position: relative; flex: 1 1 auto; padding: 1rem; }
        .modal-footer { display: flex; flex-wrap: wrap; flex-shrink: 0; align-items: center; justify-content: flex-end; padding: 0.75rem; border-top: 1px solid #dee2e6; border-bottom-right-radius: calc(0.5rem - 1px); border-bottom-left-radius: calc(0.5rem - 1px); }
        .btn-close { box-sizing: content-box; width: 1em; height: 1em; padding: 0.25em 0.25em; color: #000; background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat; border: 0; border-radius: 0.25rem; opacity: 0.5; cursor: pointer; }
        .btn-close:hover { color: #000; text-decoration: none; opacity: 0.75; }
        .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
        .modal-header .btn-close { padding: 0.5rem 0.5rem; margin: -0.5rem -0.5rem -0.5rem auto; }
    </style>
</head>

<body>
	<div class="wrapper">
		<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="{{ route('guru.dashboard') }}">
					<img src="{{ asset('logoskolabs.png') }}" alt="Logo" style="height: 24px; margin-right: 8px; vertical-align: middle; filter: brightness(0) invert(1);">
					<span class="align-middle">SKOLABS CBT</span>
					<span class="role-badge">Guru</span>
				</a>

                {{-- Guru chip --}}
                @php $guru = auth('guru')->user(); @endphp
                <div class="guru-chip">
                    @if($guru->photo)
                        <img src="{{ asset($guru->photo) }}" alt="{{ $guru->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                    @else
                        <div class="avatar">{{ strtoupper(substr($guru->name, 0, 1)) }}</div>
                    @endif
                    <div class="info">
                        <div class="name">{{ $guru->name }}</div>
                        <div class="role">{{ $guru->nip ?: 'Guru' }}</div>
                    </div>
                </div>

								@php
					$isUjianActive = request()->routeIs('guru.bank-soal.*', 'guru.exam-schedule.*', 'guru.exam-event.*', 'guru.exam-status.*', 'guru.monitoring.*');
					$isPengolahanActive = request()->routeIs('guru.processing.*');
				@endphp
				<ul class="sidebar-nav" id="sidebar-nav">
					<li class="sidebar-header">Menu Utama</li>
					<li class="sidebar-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.dashboard') }}">
							<i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					<li class="sidebar-item {{ $isUjianActive ? 'active' : '' }}">
						<a href="#ujian" data-bs-target="#ujian" data-bs-toggle="collapse" class="sidebar-link {{ $isUjianActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="edit-3"></i> <span class="align-middle">Ujian</span>
						</a>
						<ul id="ujian" class="sidebar-dropdown list-unstyled collapse {{ $isUjianActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('guru.bank-soal.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.bank-soal.index') }}"><i class="align-middle" data-feather="database"></i> <span class="align-middle">Bank Soal Saya</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.exam-event.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.exam-event.index') }}"><i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Event Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.exam-schedule.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.exam-schedule.index') }}"><i class="align-middle" data-feather="clock"></i> <span class="align-middle">Jadwal Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.exam-status.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.exam-status.index') }}"><i class="align-middle" data-feather="activity"></i> <span class="align-middle">Status Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.monitoring.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.monitoring.index') }}"><i class="align-middle" data-feather="monitor"></i> <span class="align-middle">Status Peserta</span></a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isPengolahanActive ? 'active' : '' }}">
						<a href="#pengolahan" data-bs-target="#pengolahan" data-bs-toggle="collapse" class="sidebar-link {{ $isPengolahanActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Pengolahan Nilai</span>
						</a>
						<ul id="pengolahan" class="sidebar-dropdown list-unstyled collapse {{ $isPengolahanActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('guru.processing.koreksi*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.processing.koreksi.index') }}"><i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Koreksi Esai</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.processing.hasil*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.processing.hasil.index') }}"><i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Hasil Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.processing.analisis*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.processing.analisis.index') }}"><i class="align-middle" data-feather="pie-chart"></i> <span class="align-middle">Analisis Soal</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.processing.leger*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.processing.leger.index') }}"><i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Leger Nilai</span></a>
							</li>
						</ul>
					</li>

					<li class="sidebar-header">Akun</li>
                    <li class="sidebar-item {{ request()->routeIs('guru.profile.edit') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.profile.edit') }}">
							<i class="align-middle" data-feather="user"></i> <span class="align-middle">Profil Saya</span>
						</a>
					</li>
					<li class="sidebar-item">
						<form id="guru-logout-form" action="{{ route('guru.logout') }}" method="POST" style="display:none">@csrf</form>
						<a class="sidebar-link" href="#" onclick="document.getElementById('guru-logout-form').submit()">
							<i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Keluar</span>
						</a>
					</li>
				</ul>
			</div>
		</nav>

		<div class="main">
			<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle"><i class="hamburger align-middle"></i></a>
				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
                                @if($guru->photo)
                                    <img src="{{ asset($guru->photo) }}" class="avatar img-fluid rounded-circle me-1" alt="{{ $guru->name }}" style="width: 32px; height: 32px; object-fit: cover;" />
                                @endif
								<span class="text-dark fw-semibold">{{ $guru->name }}</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('guru.profile.edit') }}">
                                    <i data-feather="user" class="align-middle me-1"></i> Profil
                                </a>
                                <div class="dropdown-divider"></div>
								<a class="dropdown-item" href="#" onclick="document.getElementById('guru-logout-form').submit()">
									<i data-feather="log-out" class="align-middle me-1"></i> Keluar
								</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>

			<main class="content">
				<div class="container-fluid p-0">
                    <!-- Alerts are handled by SweetAlert -->
					@yield('content')
				</div>
			</main>

			<footer class="footer">
				<div class="container-fluid">
					<div class="row text-muted">
						<div class="col-6 text-start">
							<p class="mb-0"><a class="text-muted" href="#"><strong>SKOLABS CBT</strong></a> &copy; Portal Guru</p>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
	<script src="{{ asset('assets/adminkit/js/app.js') }}"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Global Success Notification
        @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{!! session('success') !!}",
            timer: 2000,
            showConfirmButton: false
        });
        @endif

        // Global Error Notification
        @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "{!! session('error') !!}",
            timer: 3000,
            showConfirmButton: false
        });
        @endif

        // Global Validation Errors Notification
        @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            html: '<ul style="text-align:left; margin:0; padding-left:1.2rem; font-size:0.9rem;">' +
                  @foreach($errors->all() as $error)
                  '<li>{{ $error }}</li>' +
                  @endforeach
                  '</ul>',
            confirmButtonText: 'Tutup'
        });
        @endif

        // Global Confirm Delete Override (Intercept onclick with confirm)
        document.addEventListener('DOMContentLoaded', function() {
            document.body.addEventListener('click', function(e) {
                var el = e.target.closest('[onclick*="confirm("], .btn-delete');
                if (el) {
                    var onclickAttr = el.getAttribute('onclick');
                    if (onclickAttr && onclickAttr.includes('confirm(') || el.classList.contains('btn-delete')) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        
                        var form = el.closest('form');
                        var href = el.getAttribute('href');
                        
                        Swal.fire({
                            title: 'Apakah Anda Yakin?',
                            text: 'Aksi ini tidak dapat dibatalkan!',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i data-feather="trash-2"></i> Ya, Hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                el.removeAttribute('onclick'); // prevent recursion
                                if (form) {
                                    form.submit();
                                } else if (href) {
                                    window.location.href = href;
                                }
                            }
                        });
                        
                        if(typeof feather !== 'undefined') {
                            setTimeout(function() { feather.replace(); }, 10);
                        }
                    }
                }
            }, true); // Use capture phase!
        });
    </script>
    
    @stack('scripts')
</body>
</html>
