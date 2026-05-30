@extends('layouts.pengawas')

@section('title', 'Daftar Soal - ' . $bankSoal->mata_pelajaran)

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <div class="flex items-center gap-2 mb-1 text-slate-500 text-sm font-medium">
                <a href="{{ route('pengawas.bank_soal.index') }}" class="hover:text-indigo-600 transition-colors">Bank Soal</a>
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                <span class="text-indigo-600">{{ $bankSoal->kode_bank }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Soal {{ $bankSoal->mata_pelajaran }}</h1>
            <p class="text-slate-500 font-medium mt-1">Kelas: {{ $bankSoal->kelas ?: 'Semua Kelas' }}</p>
        </div>
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <button onclick="document.getElementById('modal-import').classList.remove('hidden')" 
                class="bg-emerald-50 text-emerald-600 border-2 border-emerald-100 px-4 md:px-6 py-2.5 rounded-2xl font-bold transition-all flex items-center justify-center gap-2 hover:bg-emerald-600 hover:text-white flex-1 md:flex-none text-sm">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span>Import Soal</span>
            </button>
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Soal</span>
            </button>
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
            <ul class="text-sm text-red-700 font-medium list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="space-y-6">
        @forelse ($soals as $index => $soal)
        <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden relative">
            <div class="absolute top-4 right-4 flex gap-2">
                <button onclick='editSoal(@json($soal))' class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all">
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <form action="{{ route('pengawas.bank_soal.soal.destroy', [$bankSoal->id, $soal->id]) }}" method="POST" onsubmit="return confirm('Hapus soal ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
            
            <div class="p-6 md:p-8">
                <div class="flex gap-4 mb-6">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shrink-0">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1">
                        @if($soal->gambar_soal)
                            <img src="{{ asset('assets/uploads/soal/' . $soal->gambar_soal) }}" alt="Gambar Soal" class="max-w-sm rounded-xl mb-4 border border-slate-200">
                        @endif
                        <div class="prose max-w-none text-slate-800 font-medium mb-6">
                            {!! $soal->teks_soal !!}
                        </div>

                        @if($soal->jenis_soal == 'esai')
                            <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                                <p class="text-sm font-bold text-amber-700 flex items-center gap-2">
                                    <i data-lucide="file-text" class="w-4 h-4"></i> Soal Esai (Tidak ada pilihan ganda)
                                </p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                    @php $optField = 'opsi_' . strtolower($opt); @endphp
                                    @if($soal->$optField)
                                    <div class="flex items-start gap-3 p-3 rounded-xl border {{ $soal->kunci_jawaban == $opt ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $soal->kunci_jawaban == $opt ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $opt }}
                                        </div>
                                        <div class="text-sm font-medium {{ $soal->kunci_jawaban == $opt ? 'text-emerald-800' : 'text-slate-600' }}">
                                            {!! $soal->$optField !!}
                                        </div>
                                        @if($soal->kunci_jawaban == $opt)
                                            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 ml-auto"></i>
                                        @endif
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 px-8 py-16 text-center text-slate-400 font-medium">
            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="w-8 h-8 text-slate-300"></i>
            </div>
            Belum ada soal pada Bank Soal ini.
        </div>
        @endforelse
        
        <!-- Floating Action Button -->
        <div class="fixed bottom-8 right-8 z-50">
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-4 rounded-full font-bold shadow-2xl shadow-indigo-300 hover:shadow-indigo-400 hover:scale-105 transition-all flex items-center gap-2 group">
                <i data-lucide="plus" class="w-5 h-5 transition-transform group-hover:rotate-90"></i>
                <span>Tambah Soal</span>
            </button>
        </div>
    </div>
</main>

<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 z-[100] flex items-start md:items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl p-6 md:p-10 transform transition-all my-4 md:my-8 max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="flex justify-between items-center mb-8 sticky top-0 bg-white z-10 pb-4 border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Tambah Soal</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('pengawas.bank_soal.soal.store', $bankSoal->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return validateSoalForm('add')">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Jenis Soal</label>
                    <select name="jenis_soal" id="add-jenis" required onchange="toggleJenisSoal('add')"
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="esai">Esai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Gambar Soal (Opsional)</label>
                    <input type="file" name="gambar_soal" accept="image/*"
                        class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2 ml-1">
                    <label class="block text-sm font-bold text-slate-700">Pertanyaan / Teks Soal</label>
                    <button type="button" onclick="openMathModal('add-teks')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                        <span>∑ Tulis Rumus/Matriks</span>
                    </button>
                </div>
                <textarea name="teks_soal" id="add-teks" rows="4"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium richtext"
                    placeholder="Tuliskan pertanyaan di sini..."></textarea>
            </div>

            <div id="add-pg-container" class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi A</label>
                        <button type="button" onclick="openMathModal('add-opsi-a')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_a" id="add-opsi-a" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi B</label>
                        <button type="button" onclick="openMathModal('add-opsi-b')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_b" id="add-opsi-b" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi C</label>
                        <button type="button" onclick="openMathModal('add-opsi-c')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_c" id="add-opsi-c" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi D</label>
                        <button type="button" onclick="openMathModal('add-opsi-d')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_d" id="add-opsi-d" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi E (Opsional)</label>
                        <button type="button" onclick="openMathModal('add-opsi-e')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_e" id="add-opsi-e" rows="2" class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Kunci Jawaban</label>
                    <select name="kunci_jawaban" id="add-kunci" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="">Pilih Kunci Jawaban</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                    </select>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span>Simpan Soal</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-start md:items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl p-6 md:p-10 transform transition-all my-4 md:my-8 max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="flex justify-between items-center mb-8 sticky top-0 bg-white z-10 pb-4 border-b border-slate-100">
            <h3 class="text-2xl font-bold text-slate-800">Edit Soal</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="return validateSoalForm('edit')">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Jenis Soal</label>
                    <select name="jenis_soal" id="edit-jenis" required onchange="toggleJenisSoal('edit')"
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="esai">Esai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Gambar Soal (Biarkan kosong jika tidak diubah)</label>
                    <input type="file" name="gambar_soal" accept="image/*"
                        class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
            </div>

            <div>
                <div class="flex justify-between items-center mb-2 ml-1">
                    <label class="block text-sm font-bold text-slate-700">Pertanyaan / Teks Soal</label>
                    <button type="button" onclick="openMathModal('edit-teks')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                        <span>∑ Tulis Rumus/Matriks</span>
                    </button>
                </div>
                <textarea name="teks_soal" id="edit-teks" rows="4"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium richtext"></textarea>
            </div>

            <div id="edit-pg-container" class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi A</label>
                        <button type="button" onclick="openMathModal('edit-opsi-a')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_a" id="edit-opsi-a" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi B</label>
                        <button type="button" onclick="openMathModal('edit-opsi-b')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_b" id="edit-opsi-b" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi C</label>
                        <button type="button" onclick="openMathModal('edit-opsi-c')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_c" id="edit-opsi-c" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi D</label>
                        <button type="button" onclick="openMathModal('edit-opsi-d')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_d" id="edit-opsi-d" rows="2" required class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label class="block text-sm font-bold text-slate-700">Opsi E (Opsional)</label>
                        <button type="button" onclick="openMathModal('edit-opsi-e')" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors flex items-center gap-1">
                            <span>∑ Tulis Rumus/Matriks</span>
                        </button>
                    </div>
                    <textarea name="opsi_e" id="edit-opsi-e" rows="2" class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium option-richtext"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Kunci Jawaban</label>
                    <select name="kunci_jawaban" id="edit-kunci" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                    </select>
                </div>
            </div>

            <div class="pt-6">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import -->
<div id="modal-import" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-6 md:p-10 transform transition-all my-8">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Import Soal (CSV)</h3>
            <button onclick="document.getElementById('modal-import').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('pengawas.bank_soal.soal.import', $bankSoal->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100 mb-6">
                <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-2">Petunjuk Import</p>
                <ul class="text-[11px] text-indigo-800 space-y-2 font-medium">
                    <li>1. Gunakan file format CSV (Excel CSV).</li>
                    <li>2. Kolom: <b>Teks Soal, Opsi A, Opsi B, Opsi C, Opsi D, Opsi E, Kunci Jawaban</b>.</li>
                    <li>3. Baris pertama dianggap sebagai Header (Judul Kolom).</li>
                </ul>
                <a href="{{ route('pengawas.bank_soal.soal.download_template', $bankSoal->id) }}" class="inline-block mt-4 text-xs font-bold text-indigo-600 hover:underline">Download Template CSV</a>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Pilih File CSV</label>
                <input type="file" name="file_soal" accept=".csv" required
                    class="block w-full px-5 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-emerald-100 transition-all flex justify-center items-center gap-2">
                <i data-lucide="upload" class="w-5 h-5"></i>
                <span>Mulai Import</span>
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
.math-tab-btn { padding:10px 16px; font-size:13px; font-weight:700; color:#64748b; background:none; border:none; border-bottom:2px solid transparent; cursor:pointer; transition:all .2s; outline:none; }
.math-tab-btn:hover { color:#4f46e5; }
.math-tab-btn.active-tab { color:#4f46e5; border-bottom-color:#4f46e5; }
#math-modal-overlay { position:fixed;inset:0;z-index:99999;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);display:none;align-items:center;justify-content:center; }
#math-modal-overlay.show { display:flex; }
#math-modal { background:white;border-radius:24px;padding:28px;width:100%;max-width:540px;box-shadow:0 25px 50px -12px rgba(0,0,0,.25); }
.math-grid-input { width:100%; padding:8px; border:2px solid #e2e8f0; border-radius:8px; text-align:center; font-size:14px; font-weight:600; outline:none; transition:border-color .2s; }
.math-grid-input:focus { border-color:#4f46e5; }
.math-view { display: flex; flex-direction: column; gap: 12px; }
</style>
@endpush

<!-- Visual Equation & Matrix Modal -->
<div id="math-modal-overlay" onclick="if(event.target===this)closeMathModal()">
    <div id="math-modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-size:18px;font-weight:800;color:#1e293b;margin:0;display:flex;align-items:center;gap:6px;">
                <span style="background:#4f46e5;color:white;width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;">∑</span>
                Visual Pembuat Rumus & Matriks
            </h3>
            <button onclick="closeMathModal()" style="background:none;border:none;cursor:pointer;color:#94a3b8;font-size:20px;font-weight:bold;">✕</button>
        </div>

        <div style="display:flex;gap:4px;border-bottom:2px solid #f1f5f9;margin-bottom:16px;padding-bottom:2px;">
            <button type="button" onclick="setMathType('matrix')" id="tab-btn-matrix" class="math-tab-btn active-tab">Matriks</button>
            <button type="button" onclick="setMathType('transform')" id="tab-btn-transform" class="math-tab-btn">Transformasi Geometri</button>
            <button type="button" onclick="setMathType('fraction')" id="tab-btn-fraction" class="math-tab-btn">Pecahan & Akar</button>
        </div>

        <div id="math-inputs-container" style="background:#f8fafc;padding:16px;border-radius:16px;border:1px solid #e2e8f0;margin-bottom:16px;min-height:160px;">
            <div id="view-matrix" class="math-view">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:700;color:#64748b;">Ukuran Matriks:</label>
                    <select id="matrix-size" onchange="buildMatrixGrid()" style="padding:4px 8px;border-radius:6px;border:1px solid #cbd5e1;outline:none;font-weight:bold;color:#334155;">
                        <option value="2x2">2 × 2</option>
                        <option value="3x3">3 × 3</option>
                        <option value="1x3">1 × 3</option>
                        <option value="3x1">3 × 1</option>
                        <option value="1x2">1 × 2</option>
                        <option value="2x1">2 × 1</option>
                        <option value="2x3">2 × 3</option>
                        <option value="3x2">3 × 2</option>
                    </select>
                </div>
                <div id="matrix-grid-container" style="display:grid;gap:6px;max-width:240px;margin:0 auto;"></div>
            </div>

            <div id="view-transform" class="math-view" style="display:none;">
                <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:700;color:#64748b;">Jenis Transformasi:</label>
                    <select id="transform-type" onchange="buildTransformInputs()" style="width:100%;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;font-weight:bold;color:#334155;">
                        <option value="translasi">Translasi T(a, b)</option>
                        <option value="refleksi_x">Pencerminan terhadap Sumbu X</option>
                        <option value="refleksi_y">Pencerminan terhadap Sumbu Y</option>
                        <option value="refleksi_yx">Pencerminan terhadap garis y = x</option>
                        <option value="refleksi_ynx">Pencerminan terhadap garis y = -x</option>
                        <option value="refleksi_xh">Pencerminan terhadap garis x = h</option>
                        <option value="refleksi_yk">Pencerminan terhadap garis y = k</option>
                        <option value="rotasi_o">Rotasi terhadap Pusat O(0, 0)</option>
                        <option value="rotasi_p">Rotasi terhadap Pusat P(a, b)</option>
                        <option value="dilatasi_o">Dilatasi Pusat O(0, 0) dengan skala k</option>
                        <option value="dilatasi_p">Dilatasi Pusat P(a, b) dengan skala k</option>
                    </select>
                </div>
                <div id="transform-fields-container" style="display:flex;flex-direction:column;gap:8px;"></div>
            </div>

            <div id="view-fraction" class="math-view" style="display:none;">
                <div id="fraction-fields-container" style="display:flex;flex-direction:column;gap:12px;">
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#64748b;display:block;margin-bottom:4px;">Pecahan (Fractions):</label>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <input type="text" id="frac-num" placeholder="Pembilang (Atas)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;text-align:center;">
                            <span style="font-weight:bold;color:#4f46e5;font-size:18px;">/</span>
                            <input type="text" id="frac-den" placeholder="Penyebut (Bawah)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;text-align:center;">
                        </div>
                    </div>
                    <div style="border-top:1px solid #e2e8f0;padding-top:12px;">
                        <label style="font-size:12px;font-weight:700;color:#64748b;display:block;margin-bottom:4px;">Akar & Pangkat (Roots & Powers):</label>
                        <div style="display:flex;gap:6px;">
                            <input type="text" id="root-val" placeholder="Nilai di bawah akar (√x)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                            <input type="text" id="power-base" placeholder="Basis pangkat (x)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                            <input type="text" id="power-exp" placeholder="Eksponen (ⁿ)" oninput="updateVisualMath()" style="flex:0.6;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p style="font-size:12px;font-weight:600;color:#64748b;margin:12px 0 6px;">Preview Tampilan Rumus:</p>
        <div id="math-preview" style="min-height:75px;padding:16px;background:#f1f5f9;border:1px solid #cbd5e1;border-radius:16px;font-size:1.4rem;text-align:center;display:flex;align-items:center;justify-content:center;color:#475569;font-family:'Times New Roman',serif;">Preview rumus akan muncul di sini</div>

        <div style="display:flex;gap:10px;margin-top:20px;">
            <button onclick="closeMathModal()" style="flex:1;padding:12px;border:2px solid #e2e8f0;border-radius:12px;font-weight:700;cursor:pointer;color:#64748b;background:white;">Batal</button>
            <button onclick="insertVisualMathToEditor()" style="flex:2;padding:12px;background:#4f46e5;color:white;border:none;border-radius:12px;font-weight:700;cursor:pointer;font-size:15px;box-shadow:0 4px 12px rgba(79, 70, 229, 0.3);">Masukkan Rumus</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
window.MathJax = {
    tex: { inlineMath:[['\\(','\\)']], displayMath:[['\\[','\\]']] },
    options: { skipHtmlTags:['script','noscript','style','textarea','pre'] }
};
</script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
let _mathTargetId = null;
let _activeMathType = 'matrix';

function openMathModal(targetId) {
    _mathTargetId = targetId;
    document.getElementById('math-modal-overlay').classList.add('show');
    setMathType(_activeMathType);
}

function closeMathModal() {
    document.getElementById('math-modal-overlay').classList.remove('show');
}

function setMathType(type) {
    _activeMathType = type;
    document.querySelectorAll('.math-tab-btn').forEach(btn => btn.classList.remove('active-tab'));
    const clickedBtn = document.getElementById('tab-btn-' + type);
    if (clickedBtn) clickedBtn.classList.add('active-tab');
    
    document.querySelectorAll('.math-view').forEach(view => view.style.display = 'none');
    const targetView = document.getElementById('view-' + type);
    if (targetView) targetView.style.display = 'flex';
    
    if (type === 'matrix') {
        buildMatrixGrid();
    } else if (type === 'transform') {
        buildTransformInputs();
    } else {
        updateVisualMath();
    }
}

function buildMatrixGrid() {
    const size = document.getElementById('matrix-size').value;
    const [rows, cols] = size.split('x').map(Number);
    const container = document.getElementById('matrix-grid-container');
    container.innerHTML = '';
    container.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
    
    for (let r = 0; r < rows; r++) {
        for (let c = 0; c < cols; c++) {
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'math-grid-input';
            input.placeholder = `a${r+1}${c+1}`;
            input.setAttribute('data-row', r);
            input.setAttribute('data-col', c);
            input.oninput = updateVisualMath;
            container.appendChild(input);
        }
    }
    updateVisualMath();
}

function buildTransformInputs() {
    const type = document.getElementById('transform-type').value;
    const container = document.getElementById('transform-fields-container');
    container.innerHTML = '';
    
    let html = '';
    if (type === 'translasi') {
        html = `
            <div style="display:flex;gap:8px;">
                <input type="text" id="trans-a" placeholder="a (Nilai Geser X)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                <input type="text" id="trans-b" placeholder="b (Nilai Geser Y)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
            </div>
        `;
    } else if (type === 'refleksi_xh') {
        html = `<input type="text" id="ref-h" placeholder="Nilai h (Garis x = h)" oninput="updateVisualMath()" style="padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">`;
    } else if (type === 'refleksi_yk') {
        html = `<input type="text" id="ref-k" placeholder="Nilai k (Garis y = k)" oninput="updateVisualMath()" style="padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">`;
    } else if (type === 'rotasi_o') {
        html = `<input type="text" id="rot-theta" placeholder="Sudut θ (Misal: 90, 180, 270)" oninput="updateVisualMath()" style="padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">`;
    } else if (type === 'rotasi_p') {
        html = `
            <div style="display:flex;flex-direction:column;gap:8px;">
                <input type="text" id="rot-theta" placeholder="Sudut θ (Misal: 90)" oninput="updateVisualMath()" style="padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                <div style="display:flex;gap:8px;">
                    <input type="text" id="rot-a" placeholder="Pusat a (X)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                    <input type="text" id="rot-b" placeholder="Pusat b (Y)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                </div>
            </div>
        `;
    } else if (type === 'dilatasi_o') {
        html = `<input type="text" id="dil-k" placeholder="Faktor skala k (Misal: 2, -1/2)" oninput="updateVisualMath()" style="padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">`;
    } else if (type === 'dilatasi_p') {
        html = `
            <div style="display:flex;flex-direction:column;gap:8px;">
                <input type="text" id="dil-k" placeholder="Faktor skala k (Misal: 2)" oninput="updateVisualMath()" style="padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                <div style="display:flex;gap:8px;">
                    <input type="text" id="dil-a" placeholder="Pusat a (X)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                    <input type="text" id="dil-b" placeholder="Pusat b (Y)" oninput="updateVisualMath()" style="flex:1;padding:8px;border-radius:8px;border:1px solid #cbd5e1;outline:none;">
                </div>
            </div>
        `;
    } else {
        html = `<p style="font-size:11px;color:#64748b;margin:0;">Matriks transformasi standar akan langsung digenerasikan.</p>`;
    }
    
    container.innerHTML = html;
    updateVisualMath();
}

function updateVisualMath() {
    let latex = '';
    
    if (_activeMathType === 'matrix') {
        const size = document.getElementById('matrix-size').value;
        const [rows, cols] = size.split('x').map(Number);
        const inputs = document.querySelectorAll('#matrix-grid-container input');
        
        let matrixRows = [];
        for (let r = 0; r < rows; r++) {
            let rowVals = [];
            for (let c = 0; c < cols; c++) {
                const input = Array.from(inputs).find(i => i.getAttribute('data-row') == r && i.getAttribute('data-col') == c);
                rowVals.push(input && input.value.trim() ? input.value.trim() : '?');
            }
            matrixRows.push(rowVals.join(' & '));
        }
        latex = '\\begin{bmatrix} ' + matrixRows.join(' \\\\ ') + ' \\end{bmatrix}';
    } 
    else if (_activeMathType === 'transform') {
        const type = document.getElementById('transform-type').value;
        if (type === 'translasi') {
            const a = document.getElementById('trans-a')?.value.trim() || 'a';
            const b = document.getElementById('trans-b')?.value.trim() || 'b';
            latex = `\\begin{pmatrix} x' \\\\ y' \\end{pmatrix} = \\begin{pmatrix} x \\\\ y \\end{pmatrix} + \\begin{pmatrix} ${a} \\\\ ${b} \\end{pmatrix}`;
        } else if (type === 'refleksi_x') {
            latex = `A(x, y) \\xrightarrow{M_x} A'(x, -y)`;
        } else if (type === 'refleksi_y') {
            latex = `A(x, y) \\xrightarrow{M_y} A'(-x, y)`;
        } else if (type === 'refleksi_yx') {
            latex = `A(x, y) \\xrightarrow{M_{y=x}} A'(y, x)`;
        } else if (type === 'refleksi_ynx') {
            latex = `A(x, y) \\xrightarrow{M_{y=-x}} A'(-y, -x)`;
        } else if (type === 'refleksi_xh') {
            const h = document.getElementById('ref-h')?.value.trim() || 'h';
            latex = `A(x, y) \\xrightarrow{M_{x=${h}}} A'(2(${h})-x, y)`;
        } else if (type === 'refleksi_yk') {
            const k = document.getElementById('ref-k')?.value.trim() || 'k';
            latex = `A(x, y) \\xrightarrow{M_{y=${k}}} A'(x, 2(${k})-y)`;
        } else if (type === 'rotasi_o') {
            const theta = document.getElementById('rot-theta')?.value.trim() || '\\theta';
            latex = `\\begin{pmatrix} x' \\\\ y' \\end{pmatrix} = \\begin{pmatrix} \\cos ${theta}^\\circ & -\\sin ${theta}^\\circ \\\\ \\sin ${theta}^\\circ & \\cos ${theta}^\\circ \\end{pmatrix} \\begin{pmatrix} x \\\\ y \\end{pmatrix}`;
        } else if (type === 'rotasi_p') {
            const theta = document.getElementById('rot-theta')?.value.trim() || '\\theta';
            const a = document.getElementById('rot-a')?.value.trim() || 'a';
            const b = document.getElementById('rot-b')?.value.trim() || 'b';
            latex = `\\begin{pmatrix} x' \\\\ y' \\end{pmatrix} = \\begin{pmatrix} \\cos ${theta}^\\circ & -\\sin ${theta}^\\circ \\\\ \\sin ${theta}^\\circ & \\cos ${theta}^\\circ \\end{pmatrix} \\begin{pmatrix} x - ${a} \\\\ y - ${b} \\end{pmatrix} + \\begin{pmatrix} ${a} \\\\ ${b} \\end{pmatrix}`;
        } else if (type === 'dilatasi_o') {
            const k = document.getElementById('dil-k')?.value.trim() || 'k';
            latex = `\\begin{pmatrix} x' \\\\ y' \\end{pmatrix} = \\begin{pmatrix} ${k} & 0 \\\\ 0 & ${k} \\end{pmatrix} \\begin{pmatrix} x \\\\ y \\end{pmatrix}`;
        } else if (type === 'dilatasi_p') {
            const k = document.getElementById('dil-k')?.value.trim() || 'k';
            const a = document.getElementById('dil-a')?.value.trim() || 'a';
            const b = document.getElementById('dil-b')?.value.trim() || 'b';
            latex = `\\begin{pmatrix} x' \\\\ y' \\end{pmatrix} = ${k} \\begin{pmatrix} x - ${a} \\\\ y - ${b} \\end{pmatrix} + \\begin{pmatrix} ${a} \\\\ ${b} \\end{pmatrix}`;
        }
    } 
    else if (_activeMathType === 'fraction') {
        const num = document.getElementById('frac-num').value.trim();
        const den = document.getElementById('frac-den').value.trim();
        const root = document.getElementById('root-val').value.trim();
        const base = document.getElementById('power-base').value.trim();
        const exp = document.getElementById('power-exp').value.trim();
        
        let parts = [];
        if (num || den) {
            parts.push('\\frac{' + (num || '?') + '}{' + (den || '?') + '}');
        }
        if (root) {
            parts.push('\\sqrt{' + root + '}');
        }
        if (base || exp) {
            parts.push((base || '?') + '^{' + (exp || '?') + '}');
        }
        
        latex = parts.join(' ');
    }
    
    const previewEl = document.getElementById('math-preview');
    if (!latex || latex === '\\begin{bmatrix} ? \\end{bmatrix}') {
        previewEl.innerHTML = 'Preview rumus akan muncul di sini';
        previewEl.setAttribute('data-latex', '');
    } else {
        previewEl.innerHTML = '\\[' + latex + '\\]';
        previewEl.setAttribute('data-latex', latex);
        if (window.MathJax) {
            MathJax.typesetPromise([previewEl]).catch(err => console.log(err));
        }
    }
}

function insertVisualMathToEditor() {
    const previewEl = document.getElementById('math-preview');
    const latex = previewEl.getAttribute('data-latex');
    if (!latex) return;
    
    const mathHtml = '\\(' + latex + '\\)';
    if (_mathTargetId) {
        const ed = tinymce.get(_mathTargetId);
        if (ed) {
            ed.insertContent(mathHtml);
            ed.save();
        } else {
            const el = document.getElementById(_mathTargetId);
            if (el) el.value += mathHtml;
        }
    }
    closeMathModal();
}

tinymce.init({
    selector: '.richtext',
    menubar: false,
    plugins: 'lists link charmap image',
    toolbar: 'undo redo | bold italic underline | superscript subscript | image | charmap | alignleft aligncenter alignright | bullist numlist',
    height: 220,
    branding: false,
    paste_data_images: true,
    images_upload_handler: function (blobInfo, success, failure) {
        return new Promise(function (resolve, reject) {
            const base64 = "data:" + blobInfo.blob().type + ";base64," + blobInfo.base64();
            if (typeof success === 'function') success(base64);
            resolve(base64);
        });
    },
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    }
});

tinymce.init({
    selector: '.option-richtext',
    menubar: false,
    plugins: 'lists link charmap image',
    toolbar: 'bold italic | image | undo redo',
    height: 140,
    branding: false,
    paste_data_images: true,
    images_upload_handler: function (blobInfo, success, failure) {
        return new Promise(function (resolve, reject) {
            const base64 = "data:" + blobInfo.blob().type + ";base64," + blobInfo.base64();
            if (typeof success === 'function') success(base64);
            resolve(base64);
        });
    },
    setup: function(editor) {
        editor.on('change', function() {
            editor.save();
        });
    }
});
</script>
@endpush

<script>
function toggleJenisSoal(mode) {
    const jenis = document.getElementById(mode + '-jenis').value;
    const pgContainer = document.getElementById(mode + '-pg-container');
    const kunci = document.getElementById(mode + '-kunci');
    const optionKeys = ['a', 'b', 'c', 'd'];

    if (jenis === 'esai') {
        pgContainer.classList.add('hidden');
        if (kunci) {
            kunci.removeAttribute('required');
            kunci.value = '';
        }
        optionKeys.forEach(key => {
            const el = document.getElementById(mode + '-opsi-' + key);
            if (el) el.removeAttribute('required');
        });
    } else {
        pgContainer.classList.remove('hidden');
        if (kunci) kunci.setAttribute('required', 'required');
        optionKeys.forEach(key => {
            const el = document.getElementById(mode + '-opsi-' + key);
            if (el) el.setAttribute('required', 'required');
        });
    }
}

function validateSoalForm(mode) {
    tinymce.triggerSave();
    
    const teksVal = document.getElementById(mode + '-teks').value.trim();
    if (!teksVal || teksVal === '<p></p>') {
        alert("Pertanyaan / Teks Soal tidak boleh kosong!");
        const editor = tinymce.get(mode + '-teks');
        if (editor) editor.focus();
        return false;
    }
    
    const jenis = document.getElementById(mode + '-jenis').value;
    if (jenis === 'pilihan_ganda') {
        const optionKeys = ['a', 'b', 'c', 'd'];
        for (let key of optionKeys) {
            const input = document.getElementById(mode + '-opsi-' + key);
            const val = input ? input.value.trim() : '';
            if (!val || val === '<p></p>') {
                alert("Opsi " + key.toUpperCase() + " tidak boleh kosong!");
                const editor = tinymce.get(mode + '-opsi-' + key);
                if (editor) editor.focus();
                return false;
            }
        }
        
        const kunci = document.getElementById(mode + '-kunci').value;
        if (!kunci) {
            alert("Silakan pilih Kunci Jawaban!");
            document.getElementById(mode + '-kunci').focus();
            return false;
        }
    }
    return true;
}

function editSoal(data) {
    document.getElementById('edit-jenis').value = data.jenis_soal || 'pilihan_ganda';
    toggleJenisSoal('edit');

    document.getElementById('edit-teks').value = data.teks_soal;
    if (tinymce.get('edit-teks')) {
        tinymce.get('edit-teks').setContent(data.teks_soal);
    }
    
    const setOpsi = (id, val) => {
        const cleanVal = (val !== '-' && val !== null && val !== undefined) ? val : '';
        document.getElementById(id).value = cleanVal;
        const editor = tinymce.get(id);
        if (editor) {
            editor.setContent(cleanVal);
        }
    };
    setOpsi('edit-opsi-a', data.opsi_a);
    setOpsi('edit-opsi-b', data.opsi_b);
    setOpsi('edit-opsi-c', data.opsi_c);
    setOpsi('edit-opsi-d', data.opsi_d);
    setOpsi('edit-opsi-e', data.opsi_e);
    
    if (data.jenis_soal !== 'esai') {
        document.getElementById('edit-kunci').value = data.kunci_jawaban;
    }
    
    let url = "{{ route('pengawas.bank_soal.soal.update', ['bank_soal' => ':bank', 'soal' => ':id']) }}"
        .replace(':bank', data.bank_soal_id)
        .replace(':id', data.id);
        
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    toggleJenisSoal('add');
});
</script>
@endsection
