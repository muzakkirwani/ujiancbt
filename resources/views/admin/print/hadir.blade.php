<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir - {{ $ujian->mapel }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 1.2cm 1cm !important;
            }
            .no-print { display: none; }
            body { 
                background: white !important; 
                padding: 0 !important; 
                margin: 0 !important; 
            }
            .max-w-4xl {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            table td {
                padding: 4px 6px !important;
                height: 32px !important;
            }
            thead {
                display: table-header-group !important;
            }
            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .mt-12 {
                margin-top: 16px !important;
            }
            .h-24 {
                height: 48px !important;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body class="bg-slate-50 p-4 md:p-10 font-sans">

    <div class="max-w-4xl mx-auto bg-white p-6 md:p-12 border border-slate-200 shadow-sm overflow-x-auto">
        <!-- Header (Kop Surat) -->
        <div class="flex items-start gap-6 pb-2">
            <div class="w-28 shrink-0">
                <!-- Logo Provinsi di Kiri -->
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
                <!-- Logo Sekolah di Kanan -->
                @if($settings->logo_url)
                    <img src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" class="w-28 h-28 object-contain ml-auto">
                @endif
            </div>
        </div>
        <!-- Garis Kop Ganda -->
        <div class="border-b-4 border-black mb-0.5"></div>
        <div class="border-b border-black mb-8"></div>

        <!-- Judul Dokumen -->
        <div class="text-center mb-8">
            <h2 class="text-lg font-black uppercase underline tracking-widest">Daftar Hadir Peserta Ujian</h2>
        </div>

        <!-- Info -->
        <div class="grid grid-cols-2 gap-x-16 gap-y-3 mb-10 text-[13px] font-bold">
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
                <span class="w-32 uppercase tracking-tighter text-black">Ruang</span>
                <span class="mr-3">:</span>
                <span class="text-slate-900 flex-1">{{ $ruang->nama_ruang ?? '-' }}</span>
            </div>
            <div class="flex items-start">
                <span class="w-32 uppercase tracking-tighter text-black">Waktu / Sesi</span>
                <span class="mr-3">:</span>
                <span class="text-slate-900 flex-1">{{ $ujian->sesi->nama_sesi ?? '-' }} ({{ date('H:i', strtotime($ujian->sesi->jam_mulai)) }})</span>
            </div>
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr class="bg-slate-100 text-xs font-black uppercase">
                    <th class="w-12 text-center">No</th>
                    <th class="w-24 text-center">NISN</th>
                    <th>Nama Peserta</th>
                    <th class="w-20 text-center">Kelas</th>
                    <th class="w-48 text-center" colspan="2">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($siswa_list as $i => $s)
                <tr class="text-sm">
                    <td class="text-center h-12">{{ $i + 1 }}</td>
                    <td class="text-center">{{ $s->nisn ?: '-' }}</td>
                    <td class="font-semibold whitespace-nowrap">{{ $s->nama }}</td>
                    <td class="text-center">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                    <td class="w-24 border-r-0">
                        @if(($i + 1) % 2 !== 0)
                            <span class="text-[10px] text-slate-400 ml-1">{{ $i + 1 }}. ...........</span>
                        @endif
                    </td>
                    <td class="w-24 border-l-0">
                        @if(($i + 1) % 2 === 0)
                            <span class="text-[10px] text-slate-400 ml-1">{{ $i + 1 }}. ...........</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Footer / Signature -->
        <div class="mt-12 grid grid-cols-2 text-sm">
            <div></div>
            <div class="text-center">
                <p>Pengawas,</p>
                <div class="h-24"></div>
                <p class="font-bold border-b border-black inline-block px-10">{{ $pengawas->nama ?? '..................................................' }}</p>
                <p class="text-xs mt-1">NIP. {{ $pengawas->nip ?? '..........................................' }}</p>
            </div>
        </div>
    </div>

    <!-- Print Button Floating -->
    <div class="no-print fixed bottom-6 right-6">
        <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-2xl shadow-lg transition-all flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            <span>Cetak Dokumen</span>
        </button>
    </div>

</body>
</html>
