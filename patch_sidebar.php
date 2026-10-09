<?php
$file = 'resources/views/layouts/admin.blade.php';
$content = file_get_contents($file);

$newSidebar = <<<'HTML'
				<ul class="sidebar-nav" id="sidebar-nav">
					<li class="sidebar-header">Main</li>
					<li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('admin.dashboard') }}">
							<i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					@php
						$isDataMasterActive = request()->routeIs('admin.peserta.*', 'admin.group.*', 'admin.jurusan.*', 'admin.subject.*', 'admin.guru.*', 'admin.user.*', 'admin.religion.*');
						$isUjianActive = request()->routeIs('admin.exam-event.*', 'admin.exam-schedule.*', 'admin.exam-status.*', 'admin.monitoring.*', 'admin.bank-soal.*');
						$isPengolahanActive = request()->routeIs('admin.processing.*');
						$isPengaturanActive = request()->routeIs('admin.settings.*');
					@endphp

					<li class="sidebar-item {{ $isDataMasterActive ? 'active' : '' }}">
						<a data-bs-target="#data-master" data-bs-toggle="collapse" class="sidebar-link {{ $isDataMasterActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="database"></i> <span class="align-middle">Data Master</span>
						</a>
						<ul id="data-master" class="sidebar-dropdown list-unstyled collapse {{ $isDataMasterActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('admin.peserta.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.peserta.index') }}">Peserta</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.group.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.group.index') }}">Grup Peserta</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.jurusan.index') }}">Jurusan</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.subject.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.subject.index') }}">Mata Pelajaran</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.guru.index') }}">Data Guru</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.user.index') }}">User Admin</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.religion.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.religion.index') }}">Agama</a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isUjianActive ? 'active' : '' }}">
						<a data-bs-target="#ujian" data-bs-toggle="collapse" class="sidebar-link {{ $isUjianActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="edit-3"></i> <span class="align-middle">Ujian</span>
						</a>
						<ul id="ujian" class="sidebar-dropdown list-unstyled collapse {{ $isUjianActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('admin.exam-event.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.exam-event.index') }}">Event Ujian</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.exam-schedule.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.exam-schedule.index') }}">Jadwal Ujian</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.exam-status.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.exam-status.index') }}">Status Ujian</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.monitoring.index') }}">Status Peserta</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.bank-soal.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.bank-soal.index') }}">Bank Soal</a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isPengolahanActive ? 'active' : '' }}">
						<a data-bs-target="#pengolahan" data-bs-toggle="collapse" class="sidebar-link {{ $isPengolahanActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Pengolahan</span>
						</a>
						<ul id="pengolahan" class="sidebar-dropdown list-unstyled collapse {{ $isPengolahanActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('admin.processing.koreksi.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.koreksi.index') }}">Koreksi</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.hasil.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.hasil.index') }}">Hasil Ujian</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.leger.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.leger.index') }}">Leger Peserta</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.analisis.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.analisis.index') }}">Analisis Butir Soal</a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.setting.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.setting.index') }}">Setting Ujian</a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isPengaturanActive ? 'active' : '' }}">
						<a data-bs-target="#pengaturan" data-bs-toggle="collapse" class="sidebar-link {{ $isPengaturanActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="settings"></i> <span class="align-middle">Pengaturan</span>
						</a>
						<ul id="pengaturan" class="sidebar-dropdown list-unstyled collapse {{ $isPengaturanActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.settings.index') }}">Informasi Instansi</a>
							</li>
						</ul>
					</li>

					<li class="sidebar-header">Akun</li>
					<li class="sidebar-item">
						<form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
						<a class="sidebar-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
							<i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Logout</span>
						</a>
					</li>
				</ul>
HTML;

$pattern = '/<ul class="sidebar-nav">.*?<\/ul>/is';
$content = preg_replace($pattern, $newSidebar, $content, 1);

$css = <<<'CSS'
        .sidebar-dropdown .sidebar-item { padding-left: 1rem; }
        .sidebar-dropdown .sidebar-link { font-size: 0.8rem; font-weight: 400 !important; opacity: 0.85; padding: 0.5rem 1.5rem !important; border-left: none !important; }
        .sidebar-dropdown .sidebar-item.active .sidebar-link { opacity: 1; font-weight: 600 !important; color: var(--sidebar-item-active) !important; background: transparent !important; }
        .sidebar-dropdown .sidebar-item.active .sidebar-link::before { content: "•"; position: absolute; left: 0.5rem; color: var(--sidebar-item-active); }
        .sidebar-link[data-bs-toggle="collapse"] { position: relative; }
        .sidebar-link[data-bs-toggle="collapse"]::after {
            content: " ";
            border: solid;
            border-width: 0 2px 2px 0;
            display: inline-block;
            padding: 2.5px;
            transform: rotate(45deg);
            position: absolute;
            right: 1.5rem;
            top: 50%;
            margin-top: -4px;
            transition: all .2s ease-out;
        }
        .sidebar-link[data-bs-toggle="collapse"].collapsed::after {
            transform: rotate(-45deg);
        }
CSS;

$content = str_replace("/* Scrollbar styling */", $css . "\n\n        /* Scrollbar styling */", $content);

file_put_contents($file, $content);
echo "Patch successful\n";
?>
