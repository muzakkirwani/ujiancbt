@extends('layouts.admin')

@section('title', 'Siswa Aktif Ujian')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-6 min-h-screen text-slate-800">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">Siswa Aktif Ujian</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">Pantau pengerjaan ujian siswa secara real-time dan kelola status sesi mereka.</p>
        </div>
        <div class="w-full md:w-auto">
            <button onclick="window.location.reload()" 
                class="w-full md:w-auto bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold shadow transition-all flex items-center justify-center gap-2">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                <span>Segarkan Data</span>
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            <p class="text-xs text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @php
        $activeCount = 0;
        $disconnectedCount = 0;
        $completedCount = 0;
        $resetCount = 0;
        
        $totalTerjawabAll = 0;
        $totalSoalsAll = 0;

        foreach($siswa_aktif as $aktif) {
            $jawabanDetail = $aktif->jawaban_detail ?? [];
            $soalsCount = ($aktif->ujian && $aktif->ujian->bankSoal) ? $aktif->ujian->bankSoal->soals->count() : 0;
            $terjawabCount = 0;
            foreach($jawabanDetail as $item) {
                if($item !== null && $item !== '') {
                    $terjawabCount++;
                }
            }
            $totalTerjawabAll += $terjawabCount;
            $totalSoalsAll += $soalsCount;

            // Classify states
            if ($aktif->status === 'berjalan') {
                if ($aktif->updated_at && $aktif->updated_at->gt(now()->subMinutes(3))) {
                    $activeCount++;
                } else {
                    $disconnectedCount++;
                }
            } elseif ($aktif->status === 'selesai') {
                $completedCount++;
            } elseif ($aktif->status === 'reset') {
                $resetCount++;
            }
        }
        $avgProgress = $totalSoalsAll > 0 ? round(($totalTerjawabAll / $totalSoalsAll) * 100) : 0;
    @endphp

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center shadow-inner animate-pulse">
                <i data-lucide="activity" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Sedang Mengerjakan</p>
                <h3 class="text-lg font-extrabold text-slate-800 mt-0.5">{{ $activeCount }} Siswa</h3>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shadow-inner">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Sudah Mengerjakan</p>
                <h3 class="text-lg font-extrabold text-slate-800 mt-0.5">{{ $completedCount }} Siswa</h3>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center gap-4">
            <div class="w-11 h-11 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center shadow-inner">
                <i data-lucide="wifi-off" class="w-5 h-5"></i>
            </div>
            <div>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Koneksi Terputus / Idle</p>
                <h3 class="text-lg font-extrabold text-slate-800 mt-0.5">{{ $disconnectedCount }} Siswa</h3>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-base font-bold text-slate-800">Daftar Status Ujian Siswa</h2>
            <div class="relative w-full md:w-auto">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="searchInput" placeholder="Cari siswa, kelas, mapel..." class="w-full md:w-72 pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa & Kelas</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ujian & Sesi</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Soal Terjawab</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aktivitas</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Aksi Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($siswa_aktif as $i => $aktif)
                    @php
                        $jawabanDetail = $aktif->jawaban_detail ?? [];
                        $soalsCount = ($aktif->ujian && $aktif->ujian->bankSoal) ? $aktif->ujian->bankSoal->soals->count() : 0;
                        
                        // hitung total terjawab
                        $terjawabCount = 0;
                        foreach($jawabanDetail as $item) {
                            if($item !== null && $item !== '') {
                                $terjawabCount++;
                            }
                        }

                        // Determine state
                        $isActive = ($aktif->status === 'berjalan' && $aktif->updated_at && $aktif->updated_at->gt(now()->subMinutes(3)));
                        $isDisconnected = ($aktif->status === 'berjalan' && (!$aktif->updated_at || $aktif->updated_at->lt(now()->subMinutes(3))));
                        $isCompleted = ($aktif->status === 'selesai');
                        $isReset = ($aktif->status === 'reset');
                    @endphp
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-2.5 font-medium text-xs text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-2.5">
                            <div class="font-bold text-sm text-slate-800 leading-tight">{{ $aktif->siswa->nama ?? 'Siswa' }}</div>
                            <div class="text-[10px] font-semibold text-slate-400 mt-0.5">NISN: {{ $aktif->siswa->username ?? '-' }} • {{ $aktif->siswa->kelas->nama_kelas ?? 'Tanpa Kelas' }}</div>
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="font-bold text-sm text-slate-700 leading-tight">{{ $aktif->ujian ? ($aktif->ujian->mapel ?? 'Mata Pelajaran') : 'Ujian Terhapus' }}</div>
                            <div class="text-[10px] font-semibold text-slate-400 mt-0.5">Sesi: {{ ($aktif->ujian && $aktif->ujian->sesi) ? ($aktif->ujian->sesi->nama_sesi ?? '-') : '-' }} ({{ ($aktif->ujian && $aktif->ujian->sesi) ? ($aktif->ujian->sesi->jam_mulai ?? '-') : '-' }} - {{ ($aktif->ujian && $aktif->ujian->sesi) ? ($aktif->ujian->sesi->jam_berakhir ?? '-') : '-' }})</div>
                        </td>
                        <td class="px-4 py-2.5">
                            @if ($isActive)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                    <span>Mengerjakan</span>
                                </span>
                            @elseif ($isDisconnected)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100 animate-pulse">
                                    <i data-lucide="wifi-off" class="w-3 h-3"></i>
                                    <span>Offline</span>
                                </span>
                            @elseif ($isCompleted)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    <span>Selesai</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    <span>Reset Ujian</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-xs text-slate-700">{{ $terjawabCount }}/{{ $soalsCount }}</span>
                                <div class="w-16 bg-slate-100 rounded-lg h-1.5 overflow-hidden">
                                    @php
                                        $percent = $soalsCount > 0 ? ($terjawabCount / $soalsCount) * 100 : 0;
                                    @endphp
                                    <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-2.5">
                            <span class="text-xs font-semibold text-slate-500">
                                {{ $aktif->updated_at ? $aktif->updated_at->diffForHumans() : 'Baru mulai' }}
                            </span>
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex justify-end gap-2">
                                @if ($isActive || $isDisconnected)
                                    <!-- Paksa Selesai Form -->
                                    <form action="{{ route('admin.siswa_aktif.selesai', $aktif->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin secara paksa menyelesaikan ujian untuk siswa ini? Seluruh hasil jawaban akan dikunci dan dinilai otomatis.')">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[10px] font-bold shadow-sm transition-all flex items-center gap-1">
                                            <i data-lucide="check-check" class="w-3 h-3"></i>
                                            <span>Selesai</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- Reset Sesi Form -->
                                <form action="{{ route('admin.siswa_aktif.reset', $aktif->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin me-reset pengerjaan ujian untuk siswa ini? Siswa akan mengulang ujian dari awal dan jawaban sebelumnya akan dibersihkan.')">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-[10px] font-bold shadow-sm transition-all flex items-center gap-1">
                                        <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                                        <span>Reset</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400 text-xs font-semibold italic">Tidak ada siswa yang sedang aktif ujian saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
@endsection
