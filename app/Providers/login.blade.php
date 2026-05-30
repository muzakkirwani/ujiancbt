<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ $settings->app_name ?? 'CBT' }}</title>
    @if(isset($settings) && $settings->logo_url)
        <link rel="shortcut icon" href="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" type="image/x-icon">
    @else
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .card-scroll::-webkit-scrollbar { width: 4px; }
        .card-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 4px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-white">

    <div class="w-full max-w-5xl flex flex-col md:flex-row gap-5 items-stretch justify-center">

        {{-- ══ LEFT PANEL: Jadwal Ujian ══════════════════════════════ --}}
        <div class="md:w-[56%] w-full p-5 md:p-6 rounded-3xl shadow-xl border border-slate-100 order-2 md:order-1"
             style="background-color: #1e1b4b;">



            {{-- Jadwal Header --}}
            <div class="flex items-center gap-2 mb-3">
                <div class="w-1 h-4 bg-indigo-400 rounded-full"></div>
                <h2 class="text-white font-bold text-xs uppercase tracking-widest text-[11px]">Jadwal Ujian</h2>
            </div>

            @php
                $today    = \Carbon\Carbon::today()->format('Y-m-d');
                $tomorrow = \Carbon\Carbon::tomorrow()->format('Y-m-d');
                $grouped  = $ujian_besok->groupBy('tanggal');
            @endphp

            @if($ujian_besok->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-10">
                <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center mb-3">
                    <i data-lucide="calendar-x" class="w-7 h-7 text-white/50"></i>
                </div>
                <p class="text-white/60 text-xs font-medium">Tidak ada ujian terjadwal<br>untuk hari ini / besok.</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach ($grouped as $tanggal => $ujianGroup)
                <div>
                    {{-- Date Chip --}}
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-bold px-2.5 py-0.5 rounded-xl mb-2
                        {{ $tanggal === $today ? 'bg-white text-indigo-700' : 'bg-white/15 text-white' }}">
                        <i data-lucide="calendar" class="w-2.5 h-2.5"></i>
                        {{ $tanggal === $today ? 'Hari Ini' : 'Besok' }} — {{ date('d M Y', strtotime($tanggal)) }}
                    </span>

                    {{-- Grid Kotak Kecil --}}
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($ujianGroup as $u)
                        <div class="rounded-xl p-2.5 border border-white/15 flex flex-col gap-1.5"
                             style="background: rgba(255,255,255,0.09);">
                            {{-- Mapel --}}
                            <p class="text-white font-bold text-xs leading-tight truncate" title="{{ $u->mapel }}">
                                {{ $u->mapel }}
                            </p>
                            {{-- Kelas --}}
                            <p class="text-indigo-200 text-[10px] font-medium leading-tight">
                                {{ $u->kelas->nama_kelas ?? '-' }}
                            </p>
                            <div class="flex items-center justify-between gap-1 mt-auto pt-1.5 border-t border-white/10">
                                {{-- Sesi --}}
                                <span class="bg-indigo-500/50 text-white text-[9px] font-bold px-2 py-0.5 rounded-md">
                                    {{ $u->sesi->nama_sesi ?? '-' }}
                                </span>
                                {{-- Jam --}}
                                @if($u->sesi)
                                <span class="text-indigo-200 text-[9px] font-medium">
                                    {{ date('H:i', strtotime($u->sesi->jam_mulai)) }}–{{ date('H:i', strtotime($u->sesi->jam_berakhir)) }}
                                </span>
                                @endif
                            </div>
                            {{-- Token (jika ada) --}}
                            @if($u->token)
                            <div class="flex items-center gap-1 pt-1 border-t border-white/10">
                                <i data-lucide="key-round" class="w-2.5 h-2.5 text-indigo-300 shrink-0"></i>
                                <span class="font-mono text-[9px] font-bold tracking-widest text-white/70 uppercase truncate">{{ $u->token }}</span>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ══ RIGHT PANEL: Login Form ════════════════════════════════ --}}
        <div class="md:w-[44%] w-full bg-white flex flex-col justify-center p-5 md:p-7 rounded-3xl shadow-xl border border-slate-100 order-1 md:order-2">

            {{-- School Identity Header --}}
            <div class="flex items-center justify-between gap-4 mb-5 pb-4 border-b border-slate-100">
                <div>
                    <p class="text-slate-800 font-extrabold text-base leading-tight">{{ $settings->app_name ?? 'CBT System' }}</p>
                    <p class="text-indigo-500 text-xs font-semibold leading-tight mt-0.5">{{ $settings->school_name ?? '' }}</p>
                </div>
                @if($settings->logo_url)
                    <img src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}"
                         alt="Logo" class="h-11 w-11 rounded-xl border border-slate-200 object-contain shrink-0 shadow-sm">
                @else
                    <div class="h-11 w-11 rounded-xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-indigo-600"></i>
                    </div>
                @endif
            </div>

            {{-- Login Header --}}
            <div class="mb-5">
                <h1 class="text-xl font-bold text-slate-900">Masuk ke Sistem</h1>
            </div>

            {{-- Error --}}
            @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 mb-4 flex items-start gap-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                <p class="text-xs text-red-700 font-medium leading-relaxed">{{ $errors->first() }}</p>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Username --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="text" name="username" required value="{{ old('username') }}"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-900 text-sm font-medium
                                   placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none transition-all"
                            placeholder="Masukkan username Anda">
                    </div>
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1.5 uppercase tracking-wider">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                        </div>
                        <input type="password" name="password" id="passwordInput" required
                            class="block w-full pl-11 pr-11 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-900 text-sm font-medium
                                   placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none transition-all"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-indigo-600 transition-colors">
                            <i data-lucide="eye" class="w-4 h-4" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember --}}
                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="remember"
                            class="w-4 h-4 rounded-md border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        <span class="text-sm text-slate-600 font-medium">Ingat Saya</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white font-bold py-3 rounded-xl
                           shadow-lg shadow-indigo-100/50 transition-all flex items-center justify-center gap-2 text-sm mt-1">
                    <span>Masuk Sekarang</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </button>
            </form>

            {{-- Footer note --}}
            <p class="text-[11px] text-slate-400 text-center mt-5 font-medium">
                Sistem Ujian CBT &mdash; {{ $settings->school_name ?? '' }}
            </p>
        </div>
    </div>

    <script>
        lucide.createIcons();
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const icon  = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                input.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
