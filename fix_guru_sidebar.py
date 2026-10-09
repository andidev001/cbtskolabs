import os

filepath = 'resources/views/layouts/guru.blade.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

target = """<ul class="sidebar-nav">
					<li class="sidebar-header">Menu Utama</li>
					<li class="sidebar-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.dashboard') }}">
							<i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					<li class="sidebar-header">Bank Soal</li>
					<li class="sidebar-item {{ request()->routeIs('guru.bank-soal.*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.bank-soal.index') }}">
							<i class="align-middle" data-feather="database"></i> <span class="align-middle">Bank Soal Saya</span>
						</a>
					</li>

					<li class="sidebar-header">Ujian</li>
					<li class="sidebar-item {{ request()->routeIs('guru.exam-schedule.*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.exam-schedule.index') }}">
							<i class="align-middle" data-feather="clock"></i> <span class="align-middle">Jadwal Ujian</span>
						</a>
					</li>

					<li class="sidebar-header">Pengolahan Nilai</li>
					<li class="sidebar-item {{ request()->routeIs('guru.processing.koreksi*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.processing.koreksi.index') }}">
							<i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Koreksi Esai</span>
						</a>
					</li>
					<li class="sidebar-item {{ request()->routeIs('guru.processing.hasil*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.processing.hasil.index') }}">
							<i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Hasil Ujian</span>
						</a>
					</li>

					<li class="sidebar-header">Akun</li>
					<li class="sidebar-item">
						<form id="guru-logout-form" action="{{ route('guru.logout') }}" method="POST" style="display:none">@csrf</form>
						<a class="sidebar-link" href="#" onclick="document.getElementById('guru-logout-form').submit()">
							<i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Keluar</span>
						</a>
					</li>
				</ul>"""

# handle windows CRLF just in case
content = content.replace('\r\n', '\n')
target = target.replace('\r\n', '\n')

replacement = """				@php
					$isUjianActive = request()->routeIs('guru.bank-soal.*') || request()->routeIs('guru.exam-schedule.*');
					$isPengolahanActive = request()->routeIs('guru.processing.*');
				@endphp
				<ul class="sidebar-nav">
					<li class="sidebar-header">Menu Utama</li>
					<li class="sidebar-item {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{ route('guru.dashboard') }}">
							<i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					<li class="sidebar-item {{ $isUjianActive ? 'active' : '' }}">
						<a data-bs-target="#ujian" data-bs-toggle="collapse" class="sidebar-link {{ $isUjianActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="edit-3"></i> <span class="align-middle">Ujian</span>
						</a>
						<ul id="ujian" class="sidebar-dropdown list-unstyled collapse {{ $isUjianActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('guru.bank-soal.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.bank-soal.index') }}"><i class="align-middle" data-feather="database"></i> <span class="align-middle">Bank Soal Saya</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.exam-schedule.*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.exam-schedule.index') }}"><i class="align-middle" data-feather="clock"></i> <span class="align-middle">Jadwal Ujian</span></a>
							</li>
						</ul>
					</li>

					<li class="sidebar-item {{ $isPengolahanActive ? 'active' : '' }}">
						<a data-bs-target="#pengolahan" data-bs-toggle="collapse" class="sidebar-link {{ $isPengolahanActive ? '' : 'collapsed' }}">
							<i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Pengolahan Nilai</span>
						</a>
						<ul id="pengolahan" class="sidebar-dropdown list-unstyled collapse {{ $isPengolahanActive ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
							<li class="sidebar-item {{ request()->routeIs('guru.processing.koreksi*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.processing.koreksi.index') }}"><i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Koreksi Esai</span></a>
							</li>
							<li class="sidebar-item {{ request()->routeIs('guru.processing.hasil*') ? 'active' : '' }}">
								<a class="sidebar-link" href="{{ route('guru.processing.hasil.index') }}"><i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Hasil Ujian</span></a>
							</li>
						</ul>
					</li>

					<li class="sidebar-header">Akun</li>
					<li class="sidebar-item">
						<form id="guru-logout-form" action="{{ route('guru.logout') }}" method="POST" style="display:none">@csrf</form>
						<a class="sidebar-link" href="#" onclick="document.getElementById('guru-logout-form').submit()">
							<i class="align-middle" data-feather="log-out"></i> <span class="align-middle">Keluar</span>
						</a>
					</li>
				</ul>"""

if target in content:
    content = content.replace(target, replacement)
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Sidebar updated!")
else:
    # Try finding the start and end indices
    start_idx = content.find('<ul class="sidebar-nav">')
    end_idx = content.find('</ul>', start_idx) + 5
    if start_idx != -1 and end_idx != -1:
        content = content[:start_idx] + replacement + content[end_idx:]
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print("Sidebar updated by index replacement!")
    else:
        print("Target not found.")

