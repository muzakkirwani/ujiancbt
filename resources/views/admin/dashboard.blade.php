@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Ringkasan Sistem</h1>
            <p class="text-slate-500 font-medium mt-1">Pantau performa dan ketersediaan CBT Anda.</p>
        </div>
        <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-sm font-bold text-slate-700">Sistem Aktif & Terhubung</span>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Stat Item -->
        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="h-14 w-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shrink-0">
                <i data-lucide="users" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Siswa Terdaftar</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $total_siswa }}</p>
            </div>
        </div>

        <!-- Stat Item -->
        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="h-14 w-14 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 shrink-0">
                <i data-lucide="box" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Jumlah Kelas</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $total_kelas }}</p>
            </div>
        </div>

        <!-- Stat Item -->
        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="h-14 w-14 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                <i data-lucide="layout-grid" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Jumlah Ruangan</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $total_ruang }}</p>
            </div>
        </div>

        <!-- Stat Item -->
        <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm flex items-center gap-5 hover:shadow-md transition-shadow">
            <div class="h-14 w-14 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600 shrink-0">
                <i data-lucide="calendar-check" class="w-7 h-7"></i>
            </div>
            <div>
                <p class="text-slate-400 font-bold text-xs uppercase tracking-widest">Ujian Hari Ini</p>
                <p class="text-3xl font-black text-slate-800 mt-1">{{ $total_ujian_today }}</p>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-50 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Jadwal Ujian Terdekat</h2>
            <span class="bg-slate-50 text-slate-500 border border-slate-100 px-3.5 py-1.5 rounded-full text-xs font-bold">5 Teratas</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Kelas / Sesi</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Token</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($upcoming_exams as $exam)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5 font-bold text-slate-800">{{ $exam->mapel }}</td>
                        <td class="px-8 py-5 text-slate-500 font-medium">{{ date('d M Y', strtotime($exam->tanggal)) }}</td>
                        <td class="px-8 py-5 font-medium">
                            <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-bold">{{ $exam->kelas->nama_kelas ?? '-' }}</span>
                            <span class="bg-amber-50 text-amber-600 px-3 py-1 rounded-full text-xs font-bold ml-2">{{ $exam->sesi->nama_sesi ?? '-' }}</span>
                        </td>
                        <td class="px-8 py-5"><span class="font-mono font-bold bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg border border-slate-200 uppercase tracking-widest">{{ $exam->token }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-medium italic">Tidak ada jadwal ujian terdekat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
