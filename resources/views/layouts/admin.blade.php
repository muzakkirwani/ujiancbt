<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') | {{ $settings->app_name ?? 'CBT Aplikasi' }}</title>
    @if(isset($settings) && $settings->logo_url)
        <link rel="shortcut icon" href="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" type="image/x-icon">
    @else
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @endif
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; }
        .sidebar-link.active { background-color: #4f46e5; color: white; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4); }
        .sidebar-link:not(.active):hover { background-color: #f1f5f9; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        /* Mobile Overlay */
        #sidebar-overlay { opacity: 0; pointer-events: none; transition: all 0.3s ease; }
        body.sidebar-open #sidebar-overlay { opacity: 1; pointer-events: auto; }
        body.sidebar-open { overflow: hidden; }
    </style>
    @stack('styles')
</head>
<body class="text-slate-800">
    <!-- Mobile Top Bar -->
    <div class="md:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200 sticky top-0 z-[60]">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
            </div>
            <span class="font-bold text-lg tracking-tight text-slate-800">{{ $settings->app_name ?? 'CBT Aplikasi' }}</span>
        </div>
        <button id="sidebar-toggle" class="p-2 bg-slate-50 text-slate-600 rounded-xl hover:bg-slate-100 transition-colors">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[40] md:hidden"></div>

    <aside id="main-sidebar" class="fixed left-0 top-0 h-screen w-72 bg-white border-r border-slate-200 z-50 flex flex-col transition-all duration-300 -translate-x-full md:translate-x-0">
        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-800">{{ $settings->app_name ?? 'CBT' }}</span>
            </div>
            <button id="sidebar-close" class="md:hidden p-2 text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>

        <nav id="sidebar-nav" class="flex-1 px-4 space-y-2 overflow-y-auto mt-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-2">Menu Utama</p>
            
            <a href="{{ route('admin.dashboard') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.kelas.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
                <i data-lucide="box" class="w-5 h-5"></i>
                <span>Data Kelas</span>
            </a>

            <a href="{{ route('admin.mata_pelajaran.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.mata_pelajaran.*') ? 'active' : '' }}">
                <i data-lucide="book-open" class="w-5 h-5"></i>
                <span>Mata Pelajaran</span>
            </a>

            <a href="{{ route('admin.siswa.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.siswa.*') ? 'active' : '' }}">
                <i data-lucide="users" class="w-5 h-5"></i>
                <span>Data Siswa</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span>Data Guru</span>
            </a>

            <a href="{{ route('admin.sesi.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.sesi.*') ? 'active' : '' }}">
                <i data-lucide="clock" class="w-5 h-5"></i>
                <span>Sesi Ujian</span>
            </a>

            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mt-8 mb-2">Ujian & Ruang</p>

            <a href="{{ route('admin.bank_soal.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.bank_soal.*') ? 'active' : '' }}">
                <i data-lucide="database" class="w-5 h-5"></i>
                <span>Bank Soal</span>
            </a>

            <a href="{{ route('admin.ujian.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.ujian.*') ? 'active' : '' }}">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span>Jadwal Ujian</span>
            </a>

            <a href="{{ route('admin.hasil_ujian.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.hasil_ujian.*') ? 'active' : '' }}">
                <i data-lucide="file-check-2" class="w-5 h-5"></i>
                <span>Hasil Ujian CBT</span>
            </a>

            <a href="{{ route('admin.siswa_aktif.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.siswa_aktif.*') ? 'active' : '' }}">
                <i data-lucide="users-round" class="w-5 h-5"></i>
                <span>Siswa Aktif Ujian</span>
            </a>

            <a href="{{ route('admin.ruang.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.ruang.*') ? 'active' : '' }}">
                <i data-lucide="layout-grid" class="w-5 h-5"></i>
                <span>Manajemen Ruang</span>
            </a>

            <a href="{{ route('admin.daftar_hadir.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.daftar_hadir.*') ? 'active' : '' }}">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                <span>Daftar Hadir</span>
            </a>

            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mt-8 mb-2">Konfigurasi</p>

            <a href="{{ route('admin.settings.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i data-lucide="settings" class="w-5 h-5"></i>
                <span>Pengaturan</span>
            </a>

            <a href="{{ route('admin.apk.index') }}" 
               class="sidebar-link flex items-center gap-3 px-4 py-3.5 rounded-2xl font-semibold text-slate-600 transition-all {{ request()->routeIs('admin.apk.*') ? 'active' : '' }}">
                <i data-lucide="smartphone" class="w-5 h-5"></i>
                <span>Aplikasi Android/APK</span>
            </a>
        </nav>

        <div class="p-6 border-t border-slate-100 bg-slate-50/50 md:hidden">
            <div class="flex items-center gap-3 mb-6">
                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-white shadow-sm">
                    <i data-lucide="user" class="w-6 h-6 text-indigo-600"></i>
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-slate-800 truncate">{{ auth()->user()->nama ?? 'Admin' }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ auth()->user()->role ?? 'Admin' }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="hidden">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="flex items-center justify-center gap-2 w-full py-3 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl font-bold transition-all group">
                <i data-lucide="log-out" class="w-4 h-4 group-hover:-translate-x-1 transition-transform"></i>
                <span>Keluar Sesi</span>
            </a>
        </div>
    </aside>

    <!-- Desktop Header: Top Right Navigation -->
    <header class="hidden md:flex items-center justify-between bg-white border-b border-slate-200 py-3.5 px-8 ml-0 md:ml-72 sticky top-0 z-40 shadow-sm">
        <div>
            <span class="text-xs font-semibold text-slate-400">Selamat datang kembali!</span>
        </div>
        <div class="flex items-center gap-4">
            <!-- User Profile Details -->
            <div class="flex items-center gap-2.5">
                <div class="h-9 w-9 rounded-full bg-indigo-50 flex items-center justify-center border border-indigo-100 shadow-sm">
                    <i data-lucide="user" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <div class="text-left">
                    <p class="text-xs font-bold text-slate-800 leading-none">{{ auth()->user()->nama ?? 'Admin' }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ auth()->user()->role ?? 'Admin' }}</p>
                </div>
            </div>
            
            <div class="h-5 w-px bg-slate-200"></div>
            
            <!-- Logout Button -->
            <form action="{{ route('logout') }}" method="POST" id="logout-form-top" class="hidden">
                @csrf
            </form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-top').submit();" 
               class="flex items-center gap-1.5 px-3.5 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-bold transition-all group">
                <i data-lucide="log-out" class="w-3.5 h-3.5 group-hover:-translate-x-0.5 transition-transform"></i>
                <span>Keluar Sesi</span>
            </a>
        </div>
    </header>

    @yield('content')

    <script>
        lucide.createIcons();

        const sidebar = document.getElementById('main-sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const body = document.body;

        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            body.classList.toggle('sidebar-open');
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', toggleSidebar);

        // Save and restore sidebar scroll position
        const sidebarNav = document.getElementById('sidebar-nav');
        if (sidebarNav) {
            const savedScrollTop = localStorage.getItem('sidebar-scroll-position');
            if (savedScrollTop) {
                sidebarNav.scrollTop = parseInt(savedScrollTop, 10);
            }

            sidebarNav.addEventListener('scroll', () => {
                localStorage.setItem('sidebar-scroll-position', sidebarNav.scrollTop);
            });

            document.querySelectorAll('.sidebar-link').forEach(link => {
                link.addEventListener('click', () => {
                    localStorage.setItem('sidebar-scroll-position', sidebarNav.scrollTop);
                });
            });
        }

        // Universal Table Search
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const table = document.querySelector('table');
                const trs = table.getElementsByTagName('tr');

                for (let i = 1; i < trs.length; i++) {
                    const tr = trs[i];
                    const tds = tr.getElementsByTagName('td');
                    let match = false;

                    for (let j = 0; j < tds.length; j++) {
                        if (tds[j].textContent.toLowerCase().indexOf(filter) > -1) {
                            match = true;
                            break;
                        }
                    }
                    tr.style.display = match ? '' : 'none';
                }
            });
        }
    </script>
    @stack('scripts')
</body>
</html>
