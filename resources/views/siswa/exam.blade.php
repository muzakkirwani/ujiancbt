<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ujian - {{ $ujian->mapel }}</title>
    @if(isset($settings) && $settings->logo_url)
        <link rel="shortcut icon" href="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" type="image/x-icon">
    @else
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']],
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        @keyframes spin-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 8s linear infinite;
        }
        label:has(input[type="radio"]:checked) {
            border-color: #4f46e5 !important;
            background-color: #f5f3ff !important;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <!-- STATE 1: TOKEN VERIFICATION / RESUME SCREEN -->
    <div id="token-screen" class="min-h-screen flex items-center justify-center bg-slate-50 p-4">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 border border-slate-100 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-50 rounded-full blur-2xl"></div>
            
            @if(isset($isResuming) && $isResuming)
                <div class="w-20 h-20 bg-emerald-500 rounded-[2rem] flex items-center justify-center shadow-xl shadow-emerald-200 mx-auto mb-8 relative z-10">
                    <i data-lucide="refresh-cw" class="w-10 h-10 text-white animate-spin-slow"></i>
                </div>

                <h2 class="text-3xl font-black text-slate-800 mb-2">Lanjutkan Ujian</h2>
                <p class="text-slate-600 font-semibold mb-1">{{ $ujian->mapel }}</p>
                <p class="text-slate-400 text-sm font-medium mb-8">Koneksi terputus atau perangkat mati? Jangan khawatir, semua jawaban Anda telah tersimpan aman.</p>

                <div class="space-y-6">
                    <button onclick="startExam()" id="start-btn"
                        class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-5 rounded-[2rem] shadow-lg shadow-emerald-100 transition-all flex justify-center items-center gap-3 text-lg">
                        <span>Lanjutkan Sekarang</span>
                        <i data-lucide="play-circle" class="w-6 h-6"></i>
                    </button>
                    <a href="{{ route('siswa.dashboard') }}" class="block text-slate-400 font-bold text-sm hover:text-slate-600 transition-colors">Kembali ke Dashboard</a>
                </div>
            @else
                <div class="w-20 h-20 bg-indigo-600 rounded-[2rem] flex items-center justify-center shadow-xl shadow-indigo-200 mx-auto mb-8 relative z-10">
                    <i data-lucide="key" class="w-10 h-10 text-white"></i>
                </div>

                <h2 class="text-3xl font-black text-slate-800 mb-2">{{ $ujian->mapel }}</h2>
                <p class="text-slate-500 font-medium mb-8">Masukkan 5 digit token dari pengawas untuk memulai ujian.</p>

                <div id="token-error" class="hidden bg-red-50 text-red-600 p-4 rounded-2xl mb-6 text-sm font-bold border border-red-100">
                    Token yang Anda masukkan salah!
                </div>

                <div class="space-y-6">
                    <input type="text" id="token-input" maxlength="5" autocomplete="off"
                        class="block w-full text-center text-4xl font-black tracking-[0.5em] py-5 bg-slate-50 border-2 border-slate-100 rounded-[2rem] text-indigo-600 focus:border-indigo-600 focus:bg-white outline-none transition-all uppercase placeholder-slate-200"
                        placeholder="•••••">
                    
                    <button onclick="verifyToken()" id="start-btn"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-5 rounded-[2rem] shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-3 text-lg">
                        <span>Mulai Ujian</span>
                        <i data-lucide="play" class="w-5 h-5"></i>
                    </button>
                    <a href="{{ route('siswa.dashboard') }}" class="block text-slate-400 font-bold text-sm hover:text-slate-600 transition-colors">Kembali ke Dashboard</a>
                </div>
            @endif
        </div>
    </div>

    <!-- STATE 2: EXAM ROOM -->
    <div id="exam-screen" class="hidden fixed inset-0 bg-white z-[9999] flex flex-col h-screen">
        <!-- Header -->
        <header class="bg-slate-900 text-white p-4 flex flex-col md:flex-row justify-between items-center gap-4 shadow-2xl px-6 md:px-8 shrink-0">
            <div class="flex items-center gap-4 w-full md:w-auto">
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="book-open" class="w-6 h-6 text-indigo-400"></i>
                </div>
                <div class="overflow-hidden">
                    <h1 class="font-black text-base md:text-lg leading-tight truncate">{{ $ujian->mapel }}</h1>
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $ujian->sesi->nama_sesi ?? '-' }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between md:justify-end gap-4 w-full md:w-auto">
                <div class="bg-slate-800 px-4 md:px-6 py-2 md:py-2.5 rounded-xl md:rounded-2xl border border-slate-700 flex items-center gap-3 md:gap-4 shadow-inner flex-1 md:flex-none justify-center">
                    <i data-lucide="clock" class="w-4 h-4 md:w-5 md:h-5 text-amber-400"></i>
                    <div id="timer" class="text-lg md:text-2xl font-black font-mono text-amber-400 tracking-widest min-w-[100px] md:min-w-[120px] text-center font-bold">00:00:00</div>
                </div>
                <button onclick="finishExam()" class="bg-red-600 hover:bg-red-700 text-white px-4 md:px-6 py-2 md:py-2.5 rounded-xl md:rounded-2xl font-bold transition-all shadow-lg shadow-red-900/20 active:scale-95 text-xs md:text-sm uppercase tracking-wider">
                    Selesai
                </button>
            </div>
        </header>

        <!-- Content Container -->
        <div class="flex-1 w-full bg-slate-50 relative overflow-hidden flex">
            @if($ujian->jenis_ujian == 'googleform')
                <iframe id="exam-iframe" src="" class="w-full h-full border-none"></iframe>
                <div id="iframe-loader" class="absolute inset-0 bg-white flex items-center justify-center flex-col gap-4">
                    <div class="w-12 h-12 border-4 border-slate-200 border-t-indigo-600 rounded-full animate-spin"></div>
                    <p class="text-slate-400 font-bold text-sm">Menghubungkan ke lembar soal...</p>
                </div>
            @else
                <!-- NATIVE CBT UI -->
                <form id="exam-form" action="{{ route('siswa.exam.submit', $ujian->id) }}" method="POST" class="flex-1 w-full relative overflow-hidden flex">
                @csrf
                <!-- Sidebar -->
                <div class="w-64 bg-white border-r border-slate-200 p-6 flex flex-col hidden md:flex h-full overflow-y-auto">
                    <h3 class="font-bold text-slate-800 mb-4">Navigasi Soal</h3>
                    <div class="grid grid-cols-4 gap-2">
                        @if($ujian->bankSoal && $ujian->bankSoal->soals)
                            @foreach($ujian->bankSoal->soals as $index => $soal)
                                <button type="button" onclick="goToSoal({{ $index }})" id="nav-btn-{{ $index }}" class="w-full aspect-square flex items-center justify-center rounded-xl font-bold text-sm border-2 {{ $index == 0 ? 'bg-indigo-600 border-indigo-600 text-white' : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-300' }} transition-colors">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Main Question Area -->
                <div class="flex-1 p-6 md:p-10 overflow-y-auto" id="soal-container">
                    @if($ujian->bankSoal && $ujian->bankSoal->soals && $ujian->bankSoal->soals->count() > 0)
                        @foreach($ujian->bankSoal->soals as $index => $soal)
                            <div id="soal-{{ $index }}" class="soal-item {{ $index == 0 ? '' : 'hidden' }} max-w-4xl mx-auto bg-white rounded-3xl p-8 shadow-sm border border-slate-100">
                                <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100">
                                    <span class="bg-indigo-50 text-indigo-600 font-black px-4 py-2 rounded-xl text-sm tracking-wider uppercase">Soal {{ $index + 1 }}</span>
                                </div>
                                
                                @if($soal->gambar_soal)
                                    <img src="{{ asset('assets/uploads/soal/' . $soal->gambar_soal) }}" class="max-h-64 rounded-xl mb-6 object-contain">
                                @endif
                                
                                <div class="text-lg text-slate-800 font-medium mb-8 leading-relaxed prose max-w-none">
                                    {!! $soal->teks_soal !!}
                                </div>

                                <div class="space-y-3">
                                    @php
                                        $currentAnswer = $savedAnswers[$soal->id] ?? null;
                                    @endphp
                                    
                                    @if($soal->jenis_soal == 'esai')
                                        <div class="mt-2">
                                            <textarea name="jawaban_{{ $soal->id }}" id="esai_{{ $soal->id }}" rows="6" placeholder="Ketik jawaban esai Anda di sini..."
                                                oninput="debouncedSaveEsai({{ $index }}, {{ $soal->id }}, this.value)"
                                                onchange="markAnswered({{ $index }}, {{ $soal->id }}, this.value)"
                                                class="block w-full px-5 py-4 bg-slate-50 border-2 border-slate-200 rounded-2xl text-slate-900 focus:border-indigo-500 focus:ring-indigo-500 outline-none transition-all font-medium">{{ $currentAnswer }}</textarea>
                                        </div>
                                    @else
                                        @foreach(['a' => $soal->opsi_a, 'b' => $soal->opsi_b, 'c' => $soal->opsi_c, 'd' => $soal->opsi_d, 'e' => $soal->opsi_e] as $key => $opsi)
                                            @if($opsi && $opsi !== '-')
                                            <label class="flex items-start gap-4 p-4 rounded-2xl border-2 border-slate-100 cursor-pointer hover:bg-slate-50 hover:border-indigo-100 transition-all group">
                                                <input type="radio" name="jawaban_{{ $soal->id }}" value="{{ strtoupper($key) }}" 
                                                    onchange="markAnswered({{ $index }}, {{ $soal->id }}, '{{ strtoupper($key) }}')"
                                                    class="mt-1 w-5 h-5 text-indigo-600 focus:ring-indigo-500 border-slate-300" {{ $currentAnswer === strtoupper($key) ? 'checked' : '' }}>
                                                <div class="flex-1">
                                                    <span class="font-bold text-slate-700 uppercase mr-2">{{ $key }}.</span>
                                                    <span class="text-slate-600 font-medium">{!! $opsi !!}</span>
                                                </div>
                                            </label>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>

                                <div class="flex justify-between items-center mt-10 pt-6 border-t border-slate-100">
                                    <button type="button" onclick="goToSoal({{ $index - 1 }})" class="px-6 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-100 transition-colors {{ $index == 0 ? 'invisible' : '' }}">
                                        &larr; Sebelumnya
                                    </button>
                                    
                                    @if($index == $ujian->bankSoal->soals->count() - 1)
                                        <button type="button" onclick="finishExam()" class="px-8 py-3 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-emerald-200 transition-all">
                                            Selesai Ujian &rarr;
                                        </button>
                                    @else
                                        <button type="button" onclick="goToSoal({{ $index + 1 }})" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-100 transition-all">
                                            Selanjutnya &rarr;
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-10 bg-white rounded-3xl border border-slate-100 max-w-lg mx-auto mt-20">
                            <i data-lucide="alert-triangle" class="w-16 h-16 text-amber-500 mx-auto mb-4"></i>
                            <h3 class="font-bold text-2xl text-slate-800">Soal Belum Tersedia</h3>
                            <p class="text-slate-500 mt-2">Bank soal ini tidak memiliki pertanyaan. Silakan lapor ke pengawas ujian.</p>
                        </div>
                    @endif
                </div>
                </form>
            @endif
        </div>
    </div>

    <!-- Modal Time Extension -->
    <div id="modal-extend" class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md hidden">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 text-center border-4 border-red-500/20 animate-bounce">
            <div class="w-20 h-20 bg-red-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8">
                <i data-lucide="timer-off" class="w-12 h-12 text-red-500"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-800 mb-2">Waktu Habis!</h3>
            <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                Mintalah token perpanjangan (5 Menit) kepada pengawas untuk mengirimkan jawaban Anda.
            </p>
            <div class="space-y-6">
                <input type="text" id="extend-token" maxlength="5"
                    class="block w-full text-center text-4xl font-black tracking-[0.5em] py-5 bg-slate-50 border-2 border-red-100 rounded-[2rem] text-red-600 focus:border-red-600 outline-none uppercase"
                    placeholder="•••••">
                <button onclick="verifyExtensionToken()" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-5 rounded-[2rem] shadow-lg shadow-red-100 transition-all flex justify-center items-center gap-2">
                    Buka Kunci Layar
                </button>
                <button onclick="finishExam()" class="w-full mt-4 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-4 rounded-[2rem] transition-all flex justify-center items-center gap-2">
                    Atau Selesai & Kirim
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Session Expired / Connection Lost -->
    <div id="modal-session-expired" class="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-slate-900/90 backdrop-blur-md hidden">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 text-center border border-slate-100">
            <div class="w-20 h-20 bg-amber-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 animate-pulse">
                <i data-lucide="shield-alert" class="w-12 h-12 text-amber-500"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-800 mb-2">Sesi Terputus!</h3>
            <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                Sesi login Anda telah kedaluwarsa atau koneksi terputus. Silakan klik tombol di bawah untuk masuk kembali tanpa kehilangan jawaban Anda.
            </p>
            <div class="space-y-4">
                <a href="{{ route('login') }}" target="_blank" onclick="hideSessionModal()" class="block w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-5 rounded-[2rem] shadow-lg shadow-amber-100 transition-all flex justify-center items-center gap-2 text-lg">
                    Masuk Kembali (Tab Baru)
                </a>
                <button onclick="window.location.reload()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-4 rounded-[2rem] transition-all">
                    Segarkan Halaman Ini
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Force Finished -->
    <div id="modal-force-finished" class="fixed inset-0 z-[20000] flex items-center justify-center p-4 bg-slate-900/95 backdrop-blur-md hidden">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 text-center border border-slate-100">
            <div class="w-20 h-20 bg-indigo-50 rounded-[2rem] flex items-center justify-center mx-auto mb-8 animate-pulse">
                <i data-lucide="lock" class="w-12 h-12 text-indigo-600"></i>
            </div>
            <h3 class="text-3xl font-black text-slate-800 mb-2">Ujian Selesai</h3>
            <p class="text-slate-500 font-medium mb-8 leading-relaxed">
                Ujian Anda telah diselesaikan secara paksa oleh Pengawas/Admin. Hasil pengerjaan Anda telah disimpan dan dikunci.
            </p>
            <div class="w-full bg-slate-50 border border-slate-100 py-3 rounded-2xl text-xs font-bold text-slate-400 flex items-center justify-center gap-2">
                <div class="w-3.5 h-3.5 border-2 border-slate-300 border-t-indigo-600 rounded-full animate-spin"></div>
                <span>Mengalihkan ke Dashboard...</span>
            </div>
        </div>
    </div>

    <script>
        @php
            $tanggal_str = $ujian->tanggal instanceof \Carbon\Carbon ? $ujian->tanggal->format('Y-m-d') : $ujian->tanggal;
            $exam_end_timestamp = strtotime($tanggal_str . ' ' . $ujian->sesi->jam_berakhir) * 1000;
        @endphp
        let timerInterval;
        let endTime = {{ $exam_end_timestamp }};
        let examStarted = false;

        let answeredQuestions = [];
        
        function updateNavButtons(activeIndex) {
            document.querySelectorAll('[id^="nav-btn-"]').forEach((btn, idx) => {
                // Reset all states
                btn.className = 'w-full aspect-square flex items-center justify-center rounded-xl font-bold text-sm border-2 transition-all';
                
                if (answeredQuestions.includes(idx)) {
                    // Answered state
                    btn.classList.add('bg-green-500', 'border-green-500', 'text-white');
                } else {
                    // Default state
                    btn.classList.add('bg-white', 'border-slate-200', 'text-slate-600', 'hover:border-indigo-300');
                }
                
                // Active state overlay
                if (idx === activeIndex) {
                    btn.classList.add('ring-4', 'ring-indigo-200', 'scale-105');
                    if (!answeredQuestions.includes(idx)) {
                        // If active but not answered, make it indigo
                        btn.classList.remove('bg-white', 'text-slate-600', 'border-slate-200', 'hover:border-indigo-300');
                        btn.classList.add('bg-indigo-600', 'border-indigo-600', 'text-white');
                    }
                }
            });
        }

        function goToSoal(index) {
            const allSoal = document.querySelectorAll('.soal-item');
            if (index < 0 || index >= allSoal.length) return;

            allSoal.forEach(el => el.classList.add('hidden'));
            document.getElementById('soal-' + index).classList.remove('hidden');
            
            updateNavButtons(index);
        }

        let debounceTimers = {};

        function showSessionExpiredModal() {
            document.getElementById('modal-session-expired').classList.remove('hidden');
            lucide.createIcons();
        }
        
        function hideSessionModal() {
            document.getElementById('modal-session-expired').classList.add('hidden');
        }

        // Session Keep-Alive Heartbeat & Force Finish Checker
        function startHeartbeat() {
            setInterval(() => {
                fetch("{{ route('siswa.heartbeat', $ujian->id) }}")
                    .then(res => {
                        if (res.status === 401 || res.status === 419) {
                            showSessionExpiredModal();
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data && data.status === 'selesai') {
                            // Tampilkan modal selesai secara paksa
                            document.getElementById('modal-force-finished').classList.remove('hidden');
                            lucide.createIcons();
                            
                            // Keluar dari fullscreen jika aktif
                            if (document.exitFullscreen) {
                                document.exitFullscreen().catch(() => {});
                            } else if (document.webkitExitFullscreen) {
                                document.webkitExitFullscreen();
                            }
                            
                            // Redirect ke dashboard siswa setelah 3.5 detik
                            setTimeout(() => {
                                window.location.href = "{{ route('siswa.dashboard') }}";
                            }, 3500);
                        }
                    })
                    .catch(err => {
                        console.error('Heartbeat failed', err);
                    });
            }, 10000); // Ping setiap 10 detik agar deteksi responsif
        }
        
        function debouncedSaveEsai(index, soalId, value) {
            // Update the navigation button state locally immediately
            if (value && value.trim() !== '') {
                if (!answeredQuestions.includes(index)) {
                    answeredQuestions.push(index);
                }
            } else {
                answeredQuestions = answeredQuestions.filter(i => i !== index);
            }
            
            const currentActiveIndex = Array.from(document.querySelectorAll('.soal-item')).findIndex(el => !el.classList.contains('hidden'));
            updateNavButtons(currentActiveIndex);

            // Clear previous timer for this question
            if (debounceTimers[soalId]) {
                clearTimeout(debounceTimers[soalId]);
            }

            // Set new timer to save after 1 second of inactivity
            debounceTimers[soalId] = setTimeout(() => {
                fetch("{{ route('siswa.exam.autosave', $ujian->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        soal_id: soalId,
                        jawaban: value
                    })
                })
                .then(res => {
                    if (res.status === 401 || res.status === 419) {
                        showSessionExpiredModal();
                    }
                    return res.json();
                })
                .catch(err => console.error('Debounced autosave failed', err));
            }, 1000);
        }

        function markAnswered(index, soalId, jawaban) {
            if (debounceTimers[soalId]) {
                clearTimeout(debounceTimers[soalId]);
            }

            if (jawaban && jawaban.trim() !== '') {
                if (!answeredQuestions.includes(index)) {
                    answeredQuestions.push(index);
                }
            } else if (jawaban === '') {
                // Remove if empty (for essay)
                answeredQuestions = answeredQuestions.filter(i => i !== index);
            }
            
            // Re-render buttons based on current visible question
            const currentActiveIndex = Array.from(document.querySelectorAll('.soal-item')).findIndex(el => !el.classList.contains('hidden'));
            updateNavButtons(currentActiveIndex);

            // Autosave via AJAX
            if(soalId && (jawaban !== undefined && jawaban !== null)) {
                fetch("{{ route('siswa.exam.autosave', $ujian->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        soal_id: soalId,
                        jawaban: jawaban
                    })
                })
                .then(res => {
                    if (res.status === 401 || res.status === 419) {
                        showSessionExpiredModal();
                    }
                    return res.json();
                })
                .catch(err => console.error('Autosave failed', err));
            }
        }

        async function verifyToken() {
            const token = document.getElementById('token-input').value.toUpperCase();
            if (token.length !== 5) return;

            const btn = document.getElementById('start-btn');
            btn.disabled = true;
            btn.innerHTML = '<div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';

            const correctToken = '{{ $ujian->token }}';

            if (token === correctToken) {
                startExam();
            } else {
                document.getElementById('token-error').classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = '<span>Mulai Ujian</span><i data-lucide="play" class="w-5 h-5"></i>';
                document.getElementById('token-input').value = '';
                lucide.createIcons();
            }
        }

        function startExam() {
            examStarted = true;
            document.getElementById('token-screen').classList.add('hidden');
            document.getElementById('exam-screen').classList.remove('hidden');
            
            // Load Iframe if exists
            const iframe = document.getElementById('exam-iframe');
            if (iframe) {
                iframe.src = '{{ $ujian->link_ujian }}';
                iframe.onload = () => {
                    const loader = document.getElementById('iframe-loader');
                    if(loader) loader.classList.add('hidden');
                };
            }

            // Request Fullscreen
            if (document.documentElement.requestFullscreen) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            }

            startTimer();
            startHeartbeat(); // Start heartbeat check to prevent session logout
        }

        function autoSubmitExam() {
            if (timerInterval) clearInterval(timerInterval);
            
            // Exit fullscreen if active
            if (document.fullscreenElement) {
                document.exitFullscreen().catch(err => {});
            }

            // Render a premium fullscreen processing overlay
            const loaderOverlay = document.createElement('div');
            loaderOverlay.className = 'fixed inset-0 bg-slate-900/90 backdrop-blur-md z-[100000] flex flex-col items-center justify-center gap-4 text-white p-6 text-center';
            loaderOverlay.innerHTML = `
                <div class="w-16 h-16 border-4 border-slate-700 border-t-red-500 rounded-full animate-spin mb-4"></div>
                <h3 class="text-3xl font-black text-white">Waktu Ujian Telah Habis!</h3>
                <p class="text-slate-300 font-medium max-w-md">Jawaban Anda sedang disimpan aman dan sistem sedang menilai hasil pengerjaan secara otomatis. Mohon tunggu sejenak...</p>
            `;
            document.body.appendChild(loaderOverlay);

            // Allow 1.5 seconds for any pending async saves to complete, then submit the form
            setTimeout(() => {
                const form = document.getElementById('exam-form');
                if (form) {
                    form.submit();
                } else {
                    window.location.href = "{{ route('siswa.dashboard') }}";
                }
            }, 1500);
        }

        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);
            
            timerInterval = setInterval(() => {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    clearInterval(timerInterval);
                    autoSubmitExam();
                    return;
                }

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                const timeStr = String(hours).padStart(2, '0') + ":" + String(minutes).padStart(2, '0') + ":" + String(seconds).padStart(2, '0');
                const timerEl = document.getElementById('timer');
                timerEl.innerText = timeStr;

                if (distance < 300000) { // < 5 menit
                    timerEl.classList.remove('text-amber-400');
                    timerEl.classList.add('text-red-500', 'animate-pulse');
                }
            }, 1000);
        }

        function verifyExtensionToken() {
            const token = document.getElementById('extend-token').value.toUpperCase();
            const correctToken = '{{ $ujian->token }}';

            if (token === correctToken) {
                document.getElementById('modal-extend').classList.add('hidden');
                const iframe = document.getElementById('exam-iframe');
                if(iframe) iframe.classList.remove('hidden');
                const nativeUI = document.getElementById('soal-container');
                if(nativeUI) {
                    nativeUI.classList.remove('hidden');
                    if(window.innerWidth >= 768) {
                        document.querySelector('.w-64.bg-white').classList.remove('hidden');
                    }
                }
                endTime = new Date().getTime() + (5 * 60 * 1000);
                startTimer();
            } else {
                alert("Token salah!");
            }
        }

        function finishExam() {
            if (confirm("Pastikan Anda sudah memilih semua jawaban. Yakin ingin menyelesaikan ujian?")) {
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                }
                const form = document.getElementById('exam-form');
                if (form) {
                    form.submit();
                } else {
                    window.location.href = "{{ route('siswa.dashboard') }}";
                }
            }
        }

        window.onload = () => {
            // Restore answered questions array
            @foreach($savedAnswers as $savedSoalId => $savedAns)
                @if(!empty($savedAns))
                    @php
                        $savedIndex = $ujian->bankSoal->soals->search(function($item) use ($savedSoalId) {
                            return $item->id == $savedSoalId;
                        });
                    @endphp
                    @if($savedIndex !== false)
                        answeredQuestions.push({{ $savedIndex }});
                    @endif
                @endif
            @endforeach
            
            // Initial render for active question 0
            updateNavButtons(0);
        };

        lucide.createIcons();
    </script>
</body>
</html>
