@extends('layouts.admin')

@section('title', 'Rekap Hasil Ujian')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col justify-between items-start gap-2 mb-10">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Hasil Ujian CBT</h1>
        <p class="text-slate-500 font-medium mt-1">Rekapitulasi nilai siswa dari ujian yang menggunakan Bank Soal.</p>
    </header>

    <div class="space-y-4">
        @forelse ($ujians->groupBy('mapel') as $mapel => $examList)
        <details class="group bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <summary class="p-5 md:p-6 flex justify-between items-center cursor-pointer list-none [&::-webkit-details-marker]:hidden hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                        <i data-lucide="folder" class="w-6 h-6 group-open:hidden"></i>
                        <i data-lucide="folder-open" class="w-6 h-6 hidden group-open:block"></i>
                    </div>
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-slate-800">{{ $mapel }}</h2>
                        <p class="text-sm text-slate-500 font-medium">{{ $examList->count() }} Jadwal Ujian</p>
                    </div>
                </div>
                <div class="text-slate-400 group-open:rotate-180 transition-transform duration-300 bg-white border border-slate-100 p-2 rounded-xl shadow-sm">
                    <i data-lucide="chevron-down" class="w-5 h-5"></i>
                </div>
            </summary>
            
            <div class="border-t border-slate-50 px-5 md:px-8 pb-8 pt-6 bg-slate-50/30">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($examList as $u)
                    <div class="bg-white border border-slate-100 rounded-2xl p-5 hover:border-indigo-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-xl text-sm border border-indigo-100">{{ $u->kelas->nama_kelas ?? 'Semua Kelas' }}</span>
                                <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1 rounded-full text-xs font-bold shadow-sm" title="Bank Soal">
                                    {{ $u->bankSoal->kode_bank ?? '-' }}
                                </span>
                            </div>
                            <div class="mb-5 bg-slate-50 rounded-xl p-3 border border-slate-100">
                                <div class="flex items-center gap-2 mb-2 text-slate-600">
                                    <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                                    <span class="text-sm font-semibold">{{ date('d M Y', strtotime($u->tanggal)) }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-600">
                                    <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                                    <span class="text-xs font-bold uppercase tracking-wide">{{ $u->sesi->nama_sesi ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                            <a href="{{ route('admin.hasil_ujian.show', $u->id) }}" 
                                class="flex-1 flex justify-center items-center gap-2 py-2.5 bg-blue-50 text-blue-600 font-bold rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm shadow-blue-100">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                <span class="text-sm">Lihat Nilai</span>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </details>
        @empty
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-12 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 text-slate-400 shadow-inner">
                <i data-lucide="folder-x" class="w-10 h-10"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700 mb-2">Belum ada Hasil Ujian</h3>
            <p class="text-slate-500 font-medium">Belum ada data ujian CBT yang terlaksana.</p>
        </div>
        @endforelse
    </div>
</main>
<script>
    lucide.createIcons();
</script>
@endsection
