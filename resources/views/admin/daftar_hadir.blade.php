@extends('layouts.admin')

@section('title', 'Cetak Daftar Hadir')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Daftar Hadir Siswa</h1>
            <p class="text-slate-500 font-medium mt-1">Pilih ruang dan sesi ujian untuk mencetak daftar hadir peserta.</p>
        </div>
    </header>

    <!-- Sesi Filter -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button onclick="filterSesi('all')" 
            class="sesi-btn active-sesi bg-indigo-600 text-white px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shadow-md" data-sesi="all">
            Semua Sesi
        </button>
        @foreach ($sesi_list as $s)
        <button onclick="filterSesi('{{ $s->id }}')" 
            class="sesi-btn bg-slate-100 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 px-5 py-2.5 rounded-2xl text-xs font-bold transition-all" data-sesi="{{ $s->id }}">
            {{ $s->nama_sesi }} ({{ date('H:i', strtotime($s->jam_mulai)) }})
        </button>
        @endforeach
    </div>

    <!-- Room Cards -->
    <div class="space-y-6" id="ruangContainer">
        @forelse ($ruang_list as $r)
        @php
            // Get pengawas assignments per sesi for this room
            $pengawas_tugas = \App\Models\RuangPengawas::with(['user', 'sesi'])
                ->where('ruang_id', $r->id)
                ->orderBy('tanggal', 'desc')
                ->get()
                ->groupBy('sesi_id');
        @endphp
        <div class="ruang-card bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden"
             data-ruang="{{ $r->nama_ruang }}" 
             data-sesi-ids="{{ $pengawas_tugas->keys()->implode(',') }}">

            {{-- Room Header --}}
            <div class="px-6 md:px-8 py-5 bg-gradient-to-r from-indigo-50 to-white border-b border-indigo-100 flex items-center gap-4">
                <div class="w-10 h-10 bg-indigo-600 rounded-2xl flex items-center justify-center shrink-0">
                    <i data-lucide="door-open" class="w-5 h-5 text-white"></i>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-slate-800 text-base">{{ $r->nama_ruang }}</h3>
                </div>
                <span class="bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                    {{ $r->siswa_count }} Siswa
                </span>
                <a href="{{ route('admin.ruang.cetak_daftar_hadir', $r->id) }}" target="_blank"
                    class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-xs font-bold transition-all shadow-md shadow-indigo-100">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Daftar Hadir</span>
                </a>
            </div>

            {{-- Sesi rows --}}
            @if($pengawas_tugas->isNotEmpty())
            <div class="divide-y divide-slate-50">
                @foreach ($pengawas_tugas as $sesiId => $tugasList)
                @php $sesiData = $tugasList->first()->sesi ?? null; @endphp
                <div class="sesi-row px-6 md:px-8 py-4 flex flex-wrap items-center gap-4" data-sesi-id="{{ $sesiId }}">
                    <div class="flex items-center gap-2 min-w-[140px]">
                        <i data-lucide="clock" class="w-4 h-4 text-indigo-400"></i>
                        <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                            {{ $sesiData->nama_sesi ?? '-' }} ({{ $sesiData ? date('H:i', strtotime($sesiData->jam_mulai)) : '-' }})
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 flex-1">
                        <span class="text-xs font-medium text-slate-400">Pengawas:</span>
                        @foreach ($tugasList as $t)
                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-xs font-bold">
                            {{ $t->user->nama ?? '-' }}
                        </span>
                        @endforeach
                    </div>
                    <span class="text-xs text-slate-400 font-medium">
                        {{ $tugasList->first() ? date('d/m/Y', strtotime($tugasList->first()->tanggal)) : '' }}
                    </span>
                    <a href="{{ route('admin.ruang.cetak_daftar_hadir', $r->id) }}?sesi_id={{ $sesiId }}" target="_blank"
                        class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-xl text-[11px] font-bold transition-all shadow-sm">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                        <span>Cetak Sesi Ini</span>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <div class="px-6 md:px-8 py-4 text-xs text-slate-400 italic">
                Belum ada pengawas ditugaskan untuk ruangan ini.
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 px-8 py-12 text-center text-slate-400 font-medium italic">
            Belum ada ruangan dengan siswa yang di-plot.
        </div>
        @endforelse
    </div>
</main>

<script>
function filterSesi(sesiId) {
    // Update button styles
    document.querySelectorAll('.sesi-btn').forEach(btn => {
        btn.classList.remove('active-sesi', 'bg-indigo-600', 'text-white', 'shadow-md');
        btn.classList.add('bg-slate-100', 'text-slate-600');
    });
    const activeBtn = document.querySelector(`.sesi-btn[data-sesi="${sesiId}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active-sesi', 'bg-indigo-600', 'text-white', 'shadow-md');
        activeBtn.classList.remove('bg-slate-100', 'text-slate-600');
    }

    // Filter room cards
    document.querySelectorAll('.ruang-card').forEach(card => {
        if (sesiId === 'all') {
            card.style.display = '';
            card.querySelectorAll('.sesi-row').forEach(row => row.style.display = '');
        } else {
            const sesiIds = card.dataset.sesiIds.split(',');
            if (sesiIds.includes(sesiId)) {
                card.style.display = '';
                card.querySelectorAll('.sesi-row').forEach(row => {
                    row.style.display = row.dataset.sesiId === sesiId ? '' : 'none';
                });
            } else {
                card.style.display = 'none';
            }
        }
    });
}
</script>
@endsection
