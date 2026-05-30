@extends('layouts.pengawas')

@section('title', 'Detail Nilai Ujian')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <a href="{{ route('pengawas.hasil_ujian.index') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 transition-colors font-bold text-sm mb-6 no-print">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Ujian
    </a>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3 no-print">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-indigo-600 rounded-[2.5rem] shadow-xl p-8 md:p-10 text-white mb-10 relative overflow-hidden no-print">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <h1 class="text-3xl font-black mb-2">{{ $ujian->mapel }}</h1>
                <p class="text-indigo-200 font-medium text-sm flex items-center gap-4">
                    <span><i data-lucide="users" class="w-4 h-4 inline mr-1"></i> Kelas: {{ $ujian->kelas->nama_kelas ?? '-' }}</span>
                    <span><i data-lucide="calendar" class="w-4 h-4 inline mr-1"></i> {{ date('d M Y', strtotime($ujian->tanggal)) }}</span>
                    <span><i data-lucide="database" class="w-4 h-4 inline mr-1"></i> Bank Soal: {{ $ujian->bankSoal->kode_bank ?? '-' }}</span>
                </p>
            </div>
            
            <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-4 flex gap-6 text-center">
                <div>
                    <div class="text-3xl font-black">{{ $hasil->count() }}</div>
                    <div class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mt-1">Siswa Ujian</div>
                </div>
                <div class="w-px bg-white/20"></div>
                <div>
                    <div class="text-3xl font-black">{{ $hasil->count() > 0 ? number_format($hasil->avg('nilai'), 2) : '0' }}</div>
                    <div class="text-[10px] font-bold text-indigo-200 uppercase tracking-widest mt-1">Rata-rata</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-50 flex justify-between items-center no-print">
            <h2 class="text-xl font-bold text-slate-800">Rekap Nilai Siswa</h2>
            <button onclick="window.print()" class="text-indigo-600 bg-indigo-50 hover:bg-indigo-600 hover:text-white px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i> Cetak PDF
            </button>
        </div>
        
        <div class="print-kop hidden flex-col mb-4">
            <div class="flex items-start gap-6 pb-2 w-full">
                <div class="w-28 shrink-0">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bf/Coat_of_arms_of_Central_Sulawesi.png/250px-Coat_of_arms_of_Central_Sulawesi.png" class="w-28 h-28 object-contain">
                </div>
                <div class="text-center flex-1" style="font-family: 'Times New Roman', Times, serif;">
                    <p class="text-[16px] font-bold uppercase leading-tight">Pemerintah Provinsi Sulawesi Tengah</p>
                    <p class="text-[18px] font-bold uppercase leading-tight">Dinas Pendidikan</p>
                    <p class="text-[13px] font-bold uppercase leading-tight">Cabang Dinas Dikmen Wilayah II Donggala-Parimo</p>
                    <h1 class="text-2xl font-black uppercase leading-tight mt-1">{{ $settings->school_name }}</h1>
                    <p class="text-[14px] font-bold italic leading-tight mt-1">{!! nl2br(e($settings->address)) !!}</p>
                </div>
                <div class="w-28 shrink-0 text-right">
                    @if($settings->logo_url)
                        <img src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" class="w-28 h-28 object-contain ml-auto">
                    @endif
                </div>
            </div>
            
            <div class="border-b-4 border-black mb-0.5 w-full"></div>
            <div class="border-b border-black mb-6 w-full"></div>
            
            <div class="text-center mb-6 w-full">
                <h2 class="text-lg font-black uppercase underline tracking-widest">Laporan Rekapitulasi Nilai Ujian</h2>
            </div>

            <div class="grid grid-cols-2 gap-x-16 gap-y-3 mb-6 text-[13px] font-bold w-full">
                <div class="flex items-start">
                    <span class="w-32 uppercase tracking-tighter text-black">Mata Pelajaran</span>
                    <span class="mr-3">:</span>
                    <span class="text-slate-900 flex-1">{{ $ujian->mapel }}</span>
                </div>
                <div class="flex items-start">
                    <span class="w-32 uppercase tracking-tighter text-black">Tanggal</span>
                    <span class="mr-3">:</span>
                    <span class="text-slate-900 flex-1">{{ date('d F Y', strtotime($ujian->tanggal)) }}</span>
                </div>
                <div class="flex items-start">
                    <span class="w-32 uppercase tracking-tighter text-black">Kelas</span>
                    <span class="mr-3">:</span>
                    <span class="text-slate-900 flex-1">{{ $ujian->kelas->nama_kelas ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="w-32 uppercase tracking-tighter text-black">Waktu / Sesi</span>
                    <span class="mr-3">:</span>
                    <span class="text-slate-900 flex-1">{{ $ujian->sesi->nama_sesi ?? '-' }} ({{ date('H:i', strtotime($ujian->sesi->jam_mulai)) }})</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto p-0 md:p-6 print:p-0">
            <table class="w-full text-left border-collapse border border-slate-300">
                <thead>
                    <tr class="bg-slate-100 print:bg-slate-100">
                        <th class="px-4 md:px-8 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider border border-slate-300 text-center">No</th>
                        <th class="px-4 md:px-8 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider border border-slate-300">Nama Siswa</th>
                        <th class="px-4 md:px-8 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider border border-slate-300 text-center">Benar</th>
                        <th class="px-4 md:px-8 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider border border-slate-300 text-center">Salah</th>
                        <th class="px-4 md:px-8 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider border border-slate-300 text-center">Kosong</th>
                        <th class="px-4 md:px-8 py-4 text-xs font-black text-indigo-700 uppercase tracking-wider border border-slate-300 text-center">NILAI</th>
                        <th class="px-4 md:px-8 py-4 text-xs font-bold text-slate-700 uppercase tracking-wider border border-slate-300 text-center no-print">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($hasil as $index => $h)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 md:px-8 py-3 text-slate-700 font-bold border border-slate-300 text-center">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-4 md:px-8 py-3 border border-slate-300">
                            <span class="font-bold text-slate-800 text-[13px]" style="font-family: 'Times New Roman', Times, serif;">{{ $h->siswa->nama ?? 'Siswa Terhapus' }}</span>
                        </td>
                        <td class="px-4 md:px-8 py-3 border border-slate-300 text-center">
                            <span class="text-green-600 font-bold">
                                {{ $h->benar }}
                            </span>
                        </td>
                        <td class="px-4 md:px-8 py-3 border border-slate-300 text-center">
                            <span class="text-red-600 font-bold">
                                {{ $h->salah }}
                            </span>
                        </td>
                        <td class="px-4 md:px-8 py-3 border border-slate-300 text-center">
                            <span class="text-slate-600 font-bold">
                                {{ $h->kosong }}
                            </span>
                        </td>
                        <td class="px-4 md:px-8 py-3 border border-slate-300 text-center bg-slate-50">
                            <span class="text-xl font-black {{ $h->nilai >= 75 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $h->nilai }}
                            </span>
                        </td>
                        <td class="px-4 md:px-8 py-3 border border-slate-300 text-center no-print">
                            <form action="{{ route('pengawas.hasil_ujian.reset', $h->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin membuka akses ujian kembali untuk siswa ini? Nilai saat ini TIDAK akan dihapus sampai siswa mengumpulkan jawaban yang baru.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm shadow-red-100" title="Reset Ujian">
                                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-10 text-center text-slate-400 font-medium">Belum ada siswa yang menyelesaikan ujian ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @php
            $soalEsai = $ujian->bankSoal ? $ujian->bankSoal->soals->where('jenis_soal', 'esai') : collect([]);
        @endphp
        @if($soalEsai->count() > 0)
        <div class="p-6 md:p-8 border-t border-slate-100" style="page-break-before: always;">
            <div class="print-kop hidden flex-col items-center border-b-4 border-double border-slate-800 pb-4 mb-8">
                <h2 class="text-xl font-black uppercase tracking-widest text-center mt-4">Lembar Jawaban Esai Siswa</h2>
                <div class="grid grid-cols-2 gap-x-16 gap-y-2 mt-4 text-[13px] font-bold w-full text-left">
                    <div>Mata Pelajaran: {{ $ujian->mapel }}</div>
                    <div>Kelas: {{ $ujian->kelas->nama_kelas ?? '-' }}</div>
                </div>
            </div>
            
            <h2 class="text-xl font-bold text-slate-800 mb-6 uppercase tracking-wider text-center print:hidden">Laporan Jawaban Esai Siswa</h2>
            
            <div class="space-y-8">
                @foreach($hasil as $index => $h)
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 print:bg-transparent print:border-0 print:p-0 print:mb-8 print:pb-8 print:border-b-2 print:border-dashed print:border-slate-300">
                    <h3 class="font-black text-[15px] text-slate-800 mb-4" style="font-family: 'Times New Roman', Times, serif;">{{ $index + 1 }}. {{ $h->siswa->nama ?? 'Siswa Terhapus' }}</h3>
                    
                    <div class="space-y-4">
                        @foreach($soalEsai as $soal)
                            <div class="print:break-inside-avoid">
                                <div class="text-sm font-medium text-slate-700 mb-1 prose prose-sm max-w-none">{!! $soal->teks_soal !!}</div>
                                <div class="bg-white p-4 rounded-xl border border-slate-200 text-sm text-slate-800 print:border-slate-400 print:bg-transparent">
                                    <span class="font-bold text-indigo-600 print:text-black mr-2">Jawaban:</span> 
                                    {!! nl2br(e($h->jawaban_detail[$soal->id] ?? '-')) !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</main>
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 1cm !important;
        }
        body * {
            visibility: hidden;
        }
        main, main * {
            visibility: visible;
            font-family: 'Times New Roman', Times, serif !important;
        }
        main {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0 !important;
            padding: 0 !important;
        }
        .no-print, a, .w-64 {
            display: none !important;
        }
        .print-kop {
            display: flex !important;
        }
        .bg-indigo-600 {
            display: none !important;
        }
        .bg-white {
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }
        table {
            border-collapse: collapse !important;
        }
        th, td {
            border: 1px solid #000 !important;
            padding: 6px 8px !important;
        }
        th {
            font-size: 11pt !important;
        }
        td {
            font-size: 11pt !important;
        }
        .bg-slate-100 {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact; 
        }
    }
</style>
@endsection
