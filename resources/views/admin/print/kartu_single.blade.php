<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Peserta - {{ $s->nama }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            @page {
                margin: 0 !important;
            }
            body {
                background: white !important;
                padding: 1cm !important;
                margin: 0 !important;
            }
            .no-print { display: none; }
            .card-container { border: 2px solid #e2e8f0; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 md:p-8 flex flex-col items-center">
    <div class="no-print mb-8 w-full flex justify-center">
        <button onclick="window.print()" class="w-full md:w-auto bg-indigo-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg hover:bg-indigo-700 transition-all flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Cetak Kartu
        </button>
    </div>

    <!-- Kartu Peserta Design -->
    <div class="card-container bg-white w-full max-w-[500px] rounded-[2rem] shadow-2xl overflow-hidden border border-slate-200 relative">
        <!-- Header -->
        <div class="bg-indigo-600 p-6 text-white flex items-center justify-center gap-4 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="w-20 h-20 flex items-center justify-center shrink-0 relative z-10">
                @if($settings->logo_url)
                    <img src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" class="w-20 h-20 object-contain">
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#4f46e5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>
                @endif
            </div>
            <div class="text-center relative z-10 flex-1">
                <h1 class="text-[16px] font-black uppercase tracking-widest text-white mb-0.5">{{ $settings->school_name }}</h1>
                <p class="text-[14px] font-bold text-white italic leading-tight">{{ explode("\n", $settings->address)[0] }}</p>
            </div>
        </div>

        <!-- Title Section -->
        <div class="bg-slate-50 py-3 border-b border-slate-100 text-center">
            <h2 class="text-[14px] font-black uppercase tracking-[0.3em] text-black">Kartu Peserta Ujian</h2>
        </div>

        <!-- Body -->
        <div class="p-6 md:p-8 flex flex-col md:flex-row gap-6 md:gap-8 items-center md:items-start text-center md:text-left">
            <!-- Photo -->
            <div class="w-32 flex flex-col gap-4">
                <div class="w-32 h-40 bg-slate-50 rounded-2xl border-2 border-slate-100 flex items-center justify-center overflow-hidden shadow-inner">
                    @if($s->foto)
                        <img src="{{ asset('assets/uploads/users/' . $s->foto) }}" class="w-full h-full object-cover">
                    @else
                        <div class="text-slate-300 flex flex-col items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            <span class="text-[8px] font-bold uppercase tracking-widest">Pas Foto</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Info -->
            <div class="flex-1 w-full font-semibold">
                <div class="grid grid-cols-2 gap-x-4 md:gap-x-12 gap-y-6 text-slate-700">
                    <div class="space-y-4">
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Nama Peserta</p>
                            <p class="text-sm md:text-base font-extrabold text-slate-800 leading-tight uppercase">{{ $s->nama }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Tempat, Tgl Lahir</p>
                            <p class="text-[12px] md:text-sm font-bold text-slate-700">{{ $s->tempat_lahir }}, {{ date('d/m/Y', strtotime($s->tanggal_lahir)) }}</p>
                        </div>
                    </div>
                    <div class="space-y-4 pl-4 border-l border-slate-100">
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">NISN</p>
                            <p class="text-[12px] md:text-sm font-bold text-slate-700">{{ $s->nisn ?: '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-1">Kelas</p>
                            <p class="text-[12px] md:text-sm font-bold text-slate-700">{{ $s->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-dashed border-slate-200">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Username</p>
                            <p class="text-sm font-mono font-bold text-indigo-600">{{ $s->username }}</p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-2">Password</p>
                            <p class="text-sm font-mono font-bold text-slate-800">{{ $s->password_view ?? '********' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-slate-50 px-8 py-4 border-t border-slate-100 flex justify-between items-center text-[8px] text-slate-400">
            <p class="italic">*Simpan kartu ini sebagai bukti kepesertaan.</p>
            <p class="font-bold">EXAM CARD</p>
        </div>
    </div>
</body>
</html>
