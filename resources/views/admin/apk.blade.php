@extends('layouts.admin')

@section('title', 'Aplikasi Android/APK')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Android APK Wrapper</h1>
            <p class="text-slate-500 font-medium mt-1">Kemasi CBT Anda menjadi aplikasi Android Exam Browser resmi.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <!-- Control Card -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-6 md:p-10 space-y-8">
            <div class="space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i data-lucide="smartphone" class="w-8 h-8"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-800">Custom Build Engine</h2>
                <p class="text-slate-500 font-medium leading-relaxed text-sm">
                    Fitur ini mempersiapkan file source code Android Studio yang sudah dikonfigurasi dengan URL server lokal atau publik Anda secara otomatis. Anda dapat langsung mengunduh ZIP untuk dikompilasi menjadi APK.
                </p>
            </div>

            <form action="{{ route('admin.apk.download') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">URL Server CBT (Alamat IP Komputer Server)</label>
                    <input type="url" name="server_url" value="http://{{ $current_server_ip }}/cbt" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-bold">
                    <p class="text-[10px] text-slate-400 font-medium mt-2 ml-1">
                        Sistem mendeteksi IP server lokal Anda saat ini. Anda juga bisa menggantinya ke domain publik jika dihosting online.
                    </p>
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    <span>Generate & Download ZIP</span>
                </button>
            </form>
        </div>

        <!-- Features Info Card -->
        <div class="bg-slate-900 text-white rounded-[2.5rem] shadow-xl p-6 md:p-10 space-y-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-indigo-600/10 rounded-full blur-3xl"></div>
            
            <div class="space-y-2">
                <span class="text-[10px] font-black text-indigo-400 uppercase tracking-widest bg-indigo-950/50 border border-indigo-900/50 px-3.5 py-1.5 rounded-full">Proteksi Keamanan Kiosk</span>
                <h3 class="text-xl font-bold mt-4">Fitur Exam Browser Android</h3>
            </div>

            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-indigo-400 shrink-0">
                        <i data-lucide="shield-alert" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-200">Blokir Screenshot & Rekam Layar</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Mencegah siswa mengambil tangkapan layar atau melakukan perekaman video selama pengerjaan ujian berlangsung.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-indigo-400 shrink-0">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-200">Double Back-Key Confirmation</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Mencegah tombol 'Kembali' yang tidak sengaja tertekan untuk menutup lembar pengerjaan soal ujian secara sepihak.</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-indigo-400 shrink-0">
                        <i data-lucide="key-round" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-slate-200">Admin Mode Settings</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">Dilengkapi menu pengaturan URL tersembunyi yang dilindungi sandi untuk memudahkan guru mengganti alamat IP server tanpa build ulang.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
