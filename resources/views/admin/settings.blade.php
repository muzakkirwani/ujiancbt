@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Pengaturan Sistem</h1>
            <p class="text-slate-500 font-medium mt-1">Konfigurasi nama aplikasi, logo instansi, dan header kartu.</p>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl">
            <div class="flex items-center gap-3 mb-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                <p class="text-sm text-red-700 font-bold">Terjadi Kesalahan Pengisian:</p>
            </div>
            <ul class="list-disc list-inside text-xs text-red-600 font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-6 md:p-10 max-w-3xl">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <!-- Logo Preview Section -->
            <div class="flex flex-col sm:flex-row items-center gap-6 pb-8 border-b border-slate-100">
                <div class="h-24 w-24 rounded-3xl bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden shrink-0 shadow-inner">
                    @if($settings->logo_url)
                        <img src="{{ asset('assets/uploads/settings/' . $settings->logo_url) }}" class="w-full h-full object-cover">
                    @else
                        <i data-lucide="image" class="w-8 h-8 text-slate-300"></i>
                    @endif
                </div>
                <div class="text-center sm:text-left">
                    <h3 class="font-bold text-slate-800 text-lg">Logo Instansi</h3>
                    <p class="text-slate-400 text-xs mt-1 mb-4 font-medium">Format file JPG/PNG, Maks. 2MB</p>
                    <input type="file" name="logo" accept="image/*"
                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 file:cursor-pointer transition-all">
                </div>
            </div>

            <!-- Fields -->
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ $settings->app_name }}" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-bold">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Sekolah / Instansi</label>
                    <input type="text" name="school_name" value="{{ $settings->school_name }}" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-bold">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Website</label>
                        <input type="text" name="website" value="{{ $settings->website }}"
                            class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-bold">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Zona Waktu (Timezone)</label>
                        <div class="relative">
                            <select name="timezone" required
                                class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-bold appearance-none cursor-pointer">
                                <option value="Asia/Jakarta" {{ $settings->timezone == 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Asia/Jakarta)</option>
                                <option value="Asia/Makassar" {{ $settings->timezone == 'Asia/Makassar' ? 'selected' : '' }}>WITA (Asia/Makassar)</option>
                                <option value="Asia/Jayapura" {{ $settings->timezone == 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Asia/Jayapura)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-5 text-slate-500">
                                <i data-lucide="chevron-down" class="w-5 h-5"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Alamat Lengkap</label>
                    <textarea name="address" rows="3"
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-bold text-sm leading-relaxed">{{ $settings->address }}</textarea>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-8 py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span>Simpan Pengaturan</span>
                </button>
            </div>
        </form>
    </div>
</main>
@endsection
