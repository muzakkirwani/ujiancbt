<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Siswa - {{ $settings->app_name }}</title>
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
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <nav class="bg-indigo-600 border-b border-indigo-700 sticky top-0 z-50 shadow-lg shadow-indigo-600/10">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center border border-white/30 overflow-hidden">
                    @if($settings->logo_url)
                        <img src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                    @endif
                </div>
                <div>
                    <h1 class="font-bold text-lg leading-tight">{{ $settings->app_name }}</h1>
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest">Portal Siswa</p>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <div class="hidden md:flex flex-col items-end">
                    <p class="text-sm font-bold text-white uppercase">{{ $siswa->nama }}</p>
                    <p class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest font-black">{{ $siswa->kelas->nama_kelas ?? '-' }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl text-sm font-bold transition-all border border-white/20">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-6 md:p-10">
        <header class="mb-10">
            <h2 class="text-3xl font-bold text-slate-800">Ujian Tersedia</h2>
            <p class="text-slate-500 font-medium mt-1">Daftar pelaksanaan ujian untuk kelas <span class="text-indigo-600 font-bold">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>.</p>
        </header>

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl flex items-center gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($exams as $u)
                @php
                    $now = time();
                    $tanggal_str = $u->tanggal instanceof \Carbon\Carbon ? $u->tanggal->format('Y-m-d') : $u->tanggal;
                    $exam_start = strtotime($tanggal_str . ' ' . $u->sesi->jam_mulai);
                    $exam_end = strtotime($tanggal_str . ' ' . $u->sesi->jam_berakhir);
                    
                    $is_active = (date('Y-m-d') == $tanggal_str && $now >= $exam_start && $now <= $exam_end);
                    $is_past = (strtotime($tanggal_str) < strtotime(date('Y-m-d')) || ($tanggal_str == date('Y-m-d') && $now > $exam_end));
                    $is_future = (strtotime($tanggal_str) > strtotime(date('Y-m-d')) || ($tanggal_str == date('Y-m-d') && $now < $exam_start));
                    $is_finished = $hasil_ujians->has($u->id) && $hasil_ujians->get($u->id)->status == 'selesai';
                @endphp
                <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-8 flex flex-col justify-between hover:shadow-xl transition-all relative overflow-hidden group">
                    @if($is_active)
                        <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-500/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
                    @endif

                    <div>
                        <div class="flex justify-between items-start mb-6">
                            @if($is_finished)
                                <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-full">Telah Dikerjakan</span>
                            @elseif($is_active)
                                <span class="flex items-center gap-1.5 text-[10px] font-black text-green-600 uppercase tracking-widest bg-green-50 px-3 py-1 rounded-full animate-pulse">
                                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div>
                                    Sedang Berjalan
                                </span>
                            @elseif($is_past)
                                <span class="text-[10px] font-black text-red-500 uppercase tracking-widest bg-red-50 px-3 py-1 rounded-full">Ditutup</span>
                            @else
                                <span class="text-[10px] font-black text-amber-600 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-full">Mendatang</span>
                            @endif
                            
                            <span class="bg-slate-50 text-slate-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">{{ $u->sesi->nama_sesi ?? '-' }}</span>
                        </div>

                        <h3 class="text-2xl font-bold text-slate-800 mb-4 truncate" title="{{ $u->mapel }}">{{ $u->mapel }}</h3>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center gap-3 text-slate-600 font-medium">
                                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">{{ date('d M Y', strtotime($u->tanggal)) }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-slate-600 font-medium">
                                <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                </div>
                                <span class="text-sm font-semibold text-slate-700">{{ date('H:i', strtotime($u->sesi->jam_mulai)) }} - {{ date('H:i', strtotime($u->sesi->jam_berakhir)) }}</span>
                            </div>
                        </div>
                    </div>

                    @if($is_finished)
                        <button disabled class="w-full bg-blue-50 text-blue-500 font-bold py-4 rounded-2xl cursor-not-allowed border border-blue-100 uppercase tracking-widest text-xs flex justify-center items-center gap-2">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            SUDAH SELESAI
                        </button>
                    @elseif($is_active)
                        <a href="{{ route('siswa.exam', $u->id) }}" 
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2 group-hover:gap-4">
                            <span>MASUK UJIAN</span>
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    @else
                        <button disabled class="w-full bg-slate-100 text-slate-400 font-bold py-4 rounded-2xl cursor-not-allowed border border-slate-200 uppercase tracking-widest text-xs">
                            {{ $is_past ? 'UJIAN DITUTUP' : 'BELUM DIMULAI' }}
                        </button>
                    @endif
                </div>
            @empty
                <div class="col-span-full bg-white rounded-[2.5rem] p-12 text-center border border-dashed border-slate-200">
                    <i data-lucide="inbox" class="w-16 h-16 text-slate-200 mx-auto mb-4"></i>
                    <h3 class="text-xl font-bold text-slate-400">Tidak ada jadwal ujian untuk kelas Anda saat ini.</h3>
                </div>
            @endforelse
        </div>
    </main>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
