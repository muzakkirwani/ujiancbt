@extends('layouts.pengawas')

@section('title', 'Pemantauan Ujian')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-6 min-h-screen text-slate-800">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">Pemantauan Ujian</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">Pantau token dan status pelaksanaan ujian secara real-time.</p>
        </div>
        <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-slate-100 flex items-center gap-2">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
            <span class="text-xs font-bold text-slate-700">Sistem Terhubung</span>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            <p class="text-xs text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($ujian_list as $u)
            @php
                $now = time();
                $exam_date = strtotime($u->tanggal);
                $exam_start = strtotime($u->tanggal . ' ' . $u->sesi->jam_mulai);
                $exam_end = strtotime($u->tanggal . ' ' . $u->sesi->jam_berakhir);
                
                $status = "Belum Mulai";
                $status_color = "slate";
                
                if (date('Y-m-d') == $u->tanggal) {
                    if ($now >= $exam_start && $now <= $exam_end) {
                        $status = "Sedang Berjalan";
                        $status_color = "emerald";
                    } elseif ($now > $exam_end) {
                        $status = "Selesai";
                        $status_color = "red";
                    }
                } elseif (date('Y-m-d') > $u->tanggal) {
                    $status = "Selesai";
                    $status_color = "red";
                }
            @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between hover:shadow-md transition-all border-b-4 {{ $status_color == 'emerald' ? 'border-b-emerald-500' : ($status_color == 'red' ? 'border-b-red-500' : 'border-b-slate-300') }}">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <span class="bg-indigo-50 text-indigo-600 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider">
                            {{ $u->kelas->nama_kelas ?? '-' }}
                        </span>
                        <div class="flex items-center gap-1.5">
                            <div class="w-1.5 h-1.5 rounded-full {{ $status_color == 'emerald' ? 'bg-emerald-500 animate-pulse' : ($status_color == 'red' ? 'bg-red-500' : 'bg-slate-400') }}"></div>
                            <span class="text-[9px] font-black {{ $status_color == 'emerald' ? 'text-emerald-600' : ($status_color == 'red' ? 'text-red-600' : 'text-slate-400') }} uppercase tracking-widest">{{ $status }}</span>
                        </div>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 mb-1.5 truncate" title="{{ $u->mapel }}">{{ $u->mapel }}</h3>
                    <div class="space-y-1.5 mb-6">
                        <div class="flex items-center gap-2 text-slate-500 text-xs font-medium">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>{{ date('d M Y', strtotime($u->tanggal)) }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-500 text-xs font-medium">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                            <span>{{ $u->sesi->nama_sesi ?? '-' }} ({{ date('H:i', strtotime($u->sesi->jam_mulai)) }} - {{ date('H:i', strtotime($u->sesi->jam_berakhir)) }})</span>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 relative group overflow-hidden">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1.5 text-center">Token Aktif</p>
                    <div class="flex items-center justify-center gap-3">
                        <p class="text-2xl font-black text-indigo-600 font-mono tracking-[0.3em] pl-3">{{ $u->token }}</p>
                        <form action="{{ route('pengawas.token.refresh') }}" method="POST" class="absolute right-3">
                            @csrf
                            <input type="hidden" name="ujian_id" value="{{ $u->id }}">
                            <button type="submit" class="p-2 bg-white border border-slate-200 rounded-lg text-slate-400 hover:text-indigo-600 hover:border-indigo-600 transition-all shadow-sm">
                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl p-10 border border-slate-100 text-center text-slate-400 text-xs font-semibold italic">
                Belum ada jadwal pelaksanaan ujian aktif saat ini.
            </div>
        @endforelse
    </div>
</main>
@endsection
