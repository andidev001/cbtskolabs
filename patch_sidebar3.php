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
								<a class="sidebar-link" href="{{ route('admin.peserta.index') }}"><i class="align-middle" data-feather="users"></i> <span class="align-middle">Peserta</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.group.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.group.index') }}"><i class="align-middle" data-feather="layers"></i> <span class="align-middle">Grup Peserta</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.jurusan.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.jurusan.index') }}"><i class="align-middle" data-feather="briefcase"></i> <span class="align-middle">Jurusan</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.subject.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.subject.index') }}"><i class="align-middle" data-feather="book"></i> <span class="align-middle">Mata Pelajaran</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.guru.index') }}"><i class="align-middle" data-feather="users"></i> <span class="align-middle">Data Guru</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.user.index') }}"><i class="align-middle" data-feather="user-check"></i> <span class="align-middle">User Admin</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.religion.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.religion.index') }}"><i class="align-middle" data-feather="heart"></i> <span class="align-middle">Agama</span></a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isUjianActive ? 'active' : '' }}">
						<a data-bs-target="#ujian" data-bs-toggle="collapse" class="sidebar-link {{ $isUjianActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="edit-3"></i> <span class="align-middle">Ujian</span>
						</a>
						<ul id="ujian" class="sidebar-dropdown list-unstyled collapse {{ $isUjianActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('admin.exam-event.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.exam-event.index') }}"><i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Event Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.exam-schedule.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.exam-schedule.index') }}"><i class="align-middle" data-feather="clock"></i> <span class="align-middle">Jadwal Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.exam-status.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.exam-status.index') }}"><i class="align-middle" data-feather="activity"></i> <span class="align-middle">Status Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.monitoring.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.monitoring.index') }}"><i class="align-middle" data-feather="monitor"></i> <span class="align-middle">Status Peserta</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.bank-soal.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.bank-soal.index') }}"><i class="align-middle" data-feather="database"></i> <span class="align-middle">Bank Soal</span></a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isPengolahanActive ? 'active' : '' }}">
						<a data-bs-target="#pengolahan" data-bs-toggle="collapse" class="sidebar-link {{ $isPengolahanActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Pengolahan</span>
						</a>
						<ul id="pengolahan" class="sidebar-dropdown list-unstyled collapse {{ $isPengolahanActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('admin.processing.koreksi.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.koreksi.index') }}"><i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Koreksi</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.hasil.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.hasil.index') }}"><i class="align-middle" data-feather="file-text"></i> <span class="align-middle">Hasil Ujian</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.leger.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.leger.index') }}"><i class="align-middle" data-feather="grid"></i> <span class="align-middle">Leger Peserta</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.analisis.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.analisis.index') }}"><i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Analisis Butir Soal</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('admin.processing.setting.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.processing.setting.index') }}"><i class="align-middle" data-feather="settings"></i> <span class="align-middle">Setting Ujian</span></a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isPengaturanActive ? 'active' : '' }}">
						<a data-bs-target="#pengaturan" data-bs-toggle="collapse" class="sidebar-link {{ $isPengaturanActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="settings"></i> <span class="align-middle">Pengaturan</span>
						</a>
						<ul id="pengaturan" class="sidebar-dropdown list-unstyled collapse {{ $isPengaturanActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('admin.settings.index') }}"><i class="align-middle" data-feather="home"></i> <span class="align-middle">Informasi Instansi</span></a>
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

$pattern = '/<ul class="sidebar-nav" id="sidebar-nav">.*?<\/ul>\s*<\/div>\s*<\/nav>/is';
$content = preg_replace($pattern, $newSidebar . "\n\t\t\t</div>\n\t\t</nav>", $content);

file_put_contents($file, $content);
echo "Clean patch successful\n";
?>
