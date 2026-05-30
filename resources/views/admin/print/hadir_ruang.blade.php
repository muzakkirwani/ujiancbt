<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Hadir - {{ $ruang->nama_ruang }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 1.2cm 1cm !important;
            }
            .no-print { display: none !important; }
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                page-break-after: always;
                break-after: page;
            }
            .sheet:last-child {
                page-break-after: avoid;
                break-after: avoid;
            }
            table td, table th {
                padding: 4px 6px !important;
            }
            .student-row td {
                height: 30px !important;
            }
            thead {
                display: table-header-group !important;
            }
            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .sig-space {
                height: 48px !important;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        .sheet { page-break-after: always; break-after: page; }
        .sheet:last-child { page-break-after: avoid; break-after: avoid; }
    </style>
</head>
<body class="bg-slate-100 p-4 md:p-8 font-sans">

@php
    /**
     * Modes:
     * - Single sesi: $sesi is set, $semua_pengawas is null → render one sheet
     * - All sesi:    $sesi is null, $semua_pengawas is grouped collection → render one sheet per sesi
     */
    $sheets = [];
    if (isset($sesi) && $sesi) {
        $sheets[] = [
            'sesi'     => $sesi,
            'pengawas' => $pengawas,
        ];
    } elseif (isset($semua_pengawas) && $semua_pengawas && $semua_pengawas->isNotEmpty()) {
        foreach ($semua_pengawas as $sesiId => $tugasList) {
            $sheets[] = [
                'sesi'     => $tugasList->first()->sesi ?? null,
                'pengawas' => $tugasList->first()->user ?? null,
            ];
        }
    } else {
        // No sesi info at all — one blank sheet
        $sheets[] = ['sesi' => null, 'pengawas' => $pengawas ?? null];
    }
@endphp

@foreach ($sheets as $sheet)
@php $sesiSheet = $sheet['sesi']; $pengawasSheet = $sheet['pengawas']; @endphp

<div class="sheet max-w-4xl mx-auto bg-white p-6 md:p-10 border border-slate-200 shadow-sm mb-6 md:mb-12">

    {{-- ── Kop Surat ─────────────────────────────────────────── --}}
    <div class="flex items-start gap-6 pb-2">
        <div class="w-28 shrink-0">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bf/Coat_of_arms_of_Central_Sulawesi.png/250px-Coat_of_arms_of_Central_Sulawesi.png"
                 class="w-28 h-28 object-contain">
        </div>
        <div class="text-center flex-1" style="font-family:'Times New Roman',Times,serif;">
            <p class="text-[16px] font-bold uppercase leading-tight">Pemerintah Provinsi Sulawesi Tengah</p>
            <p class="text-[18px] font-bold uppercase leading-tight">Dinas Pendidikan</p>
            <p class="text-[13px] font-bold uppercase leading-tight">Cabang Dinas Dikmen Wilayah II Donggala-Parimo</p>
            <h1 class="text-2xl font-black uppercase leading-tight mt-1">{{ $settings->school_name }}</h1>
            <p class="text-[14px] font-bold italic leading-tight mt-1">{!! nl2br(e($settings->address)) !!}</p>
        </div>
        <div class="w-28 shrink-0 text-right">
            @if($settings->logo_url)
                <img src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}"
                     class="w-28 h-28 object-contain ml-auto">
            @endif
        </div>
    </div>
    <div class="border-b-4 border-black mb-0.5"></div>
    <div class="border-b border-black mb-6"></div>

    {{-- ── Judul ────────────────────────────────────────────── --}}
    <div class="text-center mb-6">
        <h2 class="text-lg font-black uppercase underline tracking-widest">Daftar Hadir Peserta Ujian</h2>
    </div>

    {{-- ── Info ─────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-x-16 gap-y-2 mb-6 text-[13px] font-bold">
        <div class="flex items-start">
            <span class="w-32 uppercase tracking-tighter">Ruang</span>
            <span class="mr-3">:</span>
            <span class="flex-1">{{ $ruang->nama_ruang }}</span>
        </div>
        <div class="flex items-start">
            <span class="w-32 uppercase tracking-tighter">Tanggal Ujian</span>
            <span class="mr-3">:</span>
            <span class="flex-1">
                {{ isset($tanggal_ujian) && $tanggal_ujian ? date('d F Y', strtotime($tanggal_ujian)) : date('d F Y') }}
            </span>
        </div>
        <div class="flex items-start">
            <span class="w-32 uppercase tracking-tighter">Mata Pelajaran</span>
            <span class="mr-3">:</span>
            <span class="flex-1">
                {{ isset($mapel_list) && $mapel_list->isNotEmpty() ? $mapel_list->implode(' / ') : '-' }}
            </span>
        </div>
        <div class="flex items-start">
            <span class="w-32 uppercase tracking-tighter">Waktu / Sesi</span>
            <span class="mr-3">:</span>
            <span class="flex-1">
                @if($sesiSheet)
                    {{ $sesiSheet->nama_sesi }}
                    ({{ date('H:i', strtotime($sesiSheet->jam_mulai)) }}
                    – {{ date('H:i', strtotime($sesiSheet->jam_berakhir)) }})
                @else
                    -
                @endif
            </span>
        </div>
    </div>

    {{-- ── Tabel Siswa ──────────────────────────────────────── --}}
    <table>
        <thead>
            <tr class="bg-slate-100 text-xs font-black uppercase">
                <th class="w-10 text-center">No</th>
                <th class="w-24 text-center">NISN</th>
                <th>Nama Peserta</th>
                <th class="w-20 text-center">Kelas</th>
                <th class="w-36 text-center">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($siswa_list as $i => $s)
            <tr class="student-row text-sm">
                <td class="text-center">{{ $i + 1 }}</td>
                <td class="text-center">{{ $s->nisn ?: '-' }}</td>
                <td class="font-semibold">{{ $s->nama }}</td>
                <td class="text-center">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                <td class="text-center">&nbsp;</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Tanda Tangan ────────────────────────────────────── --}}
    <div class="mt-10 grid grid-cols-2 text-sm">
        <div></div>
        <div class="text-center">
            <p>Pengawas,</p>
            <div class="sig-space h-20"></div>
            <p class="font-bold border-b border-black inline-block px-10">
                {{ $pengawasSheet->nama ?? '..................................................' }}
            </p>
            <p class="text-xs mt-1">
                NIP. {{ $pengawasSheet->nip ?? '..........................................' }}
            </p>
        </div>
    </div>

</div>
@endforeach

{{-- ── Tombol Cetak (layar saja) ──────────────────────────── --}}
<div class="no-print fixed bottom-6 right-6">
    <button onclick="window.print()"
        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-2xl shadow-lg transition-all flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
        </svg>
        <span>Cetak Dokumen</span>
    </button>
</div>

</body>
</html>
