@extends('layouts.admin')

@section('title', 'Bank Soal')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Bank Soal</h1>
            <p class="text-slate-500 font-medium mt-1">Kelola bank soal untuk persiapan ujian.</p>
        </div>
        <div class="flex gap-2 w-full md:w-auto">
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2 text-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Bank Soal</span>
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($bank_soals->groupBy('mata_pelajaran') as $mapel => $banks)
        <details class="group bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
            <summary class="p-5 md:p-6 flex justify-between items-center cursor-pointer list-none [&::-webkit-details-marker]:hidden hover:bg-slate-50 transition-colors">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                        <i data-lucide="folder" class="w-6 h-6 group-open:hidden"></i>
                        <i data-lucide="folder-open" class="w-6 h-6 hidden group-open:block"></i>
                    </div>
                    <div>
                        <h2 class="text-lg md:text-xl font-bold text-slate-800">{{ $mapel }}</h2>
                        <p class="text-sm text-slate-500 font-medium">{{ $banks->count() }} Bank Soal</p>
                    </div>
                </div>
                <div class="text-slate-400 group-open:rotate-180 transition-transform duration-300 bg-white border border-slate-100 p-2 rounded-xl shadow-sm">
                    <i data-lucide="chevron-down" class="w-5 h-5"></i>
                </div>
            </summary>
            
            <div class="border-t border-slate-50 px-5 md:px-8 pb-8 pt-6 bg-slate-50/30">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($banks as $b)
                    <div class="bg-white border border-slate-100 rounded-2xl p-5 hover:border-indigo-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                        <div class="flex justify-between items-start mb-4">
                            <span class="font-bold text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-xl text-sm border border-indigo-100">{{ $b->kode_bank }}</span>
                            <span class="bg-white border border-slate-200 text-slate-600 px-3 py-1 rounded-full text-xs font-bold shadow-sm">
                                {{ $b->soals_count }} Soal
                            </span>
                        </div>
                        <div class="mb-5 bg-slate-50 rounded-xl p-3 border border-slate-100">
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Kelas</p>
                            <p class="text-sm font-semibold text-slate-700 truncate" title="{{ $b->kelas ?: 'Semua Kelas' }}">{{ $b->kelas ?: 'Semua Kelas' }}</p>
                        </div>
                        <div class="flex justify-end gap-2 pt-4 border-t border-slate-100">
                            <a href="{{ route('admin.bank_soal.soal.index', $b->id) }}" 
                                class="flex-1 flex justify-center items-center gap-2 py-2 bg-blue-50 text-blue-600 font-semibold rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm shadow-blue-100" title="Kelola Soal">
                                <i data-lucide="list-checks" class="w-4 h-4"></i>
                                <span class="text-sm">Soal</span>
                            </a>
                            <button onclick='editBankSoal(@json($b))' 
                                class="p-2.5 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-600 hover:text-white transition-all shadow-sm shadow-amber-100" title="Edit">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <form action="{{ route('admin.bank_soal.destroy', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus Bank Soal ini? Semua soal di dalamnya akan terhapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2.5 bg-red-50 text-red-600 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm shadow-red-100" title="Hapus">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
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
            <h3 class="text-xl font-bold text-slate-700 mb-2">Belum ada Bank Soal</h3>
            <p class="text-slate-500 font-medium">Tambahkan bank soal baru untuk memulai.</p>
        </div>
        @endforelse
    </div>
</main>

<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-6 md:p-10 transform transition-all my-8">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Tambah Bank Soal</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('admin.bank_soal.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Kode Bank</label>
                <input type="text" name="kode_bank" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                    placeholder="Misal: MTK-X-01">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Mata Pelajaran</label>
                <select name="mata_pelajaran" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="" disabled selected>-- Pilih Mata Pelajaran --</option>
                    @foreach($mata_pelajarans as $mapel)
                        <option value="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Pilih Kelas (Bisa lebih dari satu)</label>
                <div class="grid grid-cols-2 gap-3 max-h-48 overflow-y-auto p-1">
                    @foreach($kelas_list as $k)
                    <label class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-indigo-50 transition-colors">
                        <input type="checkbox" name="kelas[]" value="{{ $k->nama_kelas }}" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-sm font-bold text-slate-700">{{ $k->nama_kelas }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 mt-2 ml-1">* Kosongkan semua pilihan jika soal berlaku untuk <b>Semua Kelas</b>.</p>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span>Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-6 md:p-10 transform transition-all my-8">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Edit Bank Soal</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Kode Bank</label>
                <input type="text" name="kode_bank" id="edit-kode" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Mata Pelajaran</label>
                <select name="mata_pelajaran" id="edit-mapel" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="" disabled>-- Pilih Mata Pelajaran --</option>
                    @foreach($mata_pelajarans as $mapel)
                        <option value="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Pilih Kelas (Bisa lebih dari satu)</label>
                <div class="grid grid-cols-2 gap-3 max-h-48 overflow-y-auto p-1">
                    @foreach($kelas_list as $k)
                    <label class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-indigo-50 transition-colors">
                        <input type="checkbox" name="kelas[]" value="{{ $k->nama_kelas }}" class="w-4 h-4 edit-kelas-checkbox text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-sm font-bold text-slate-700">{{ $k->nama_kelas }}</span>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-slate-500 mt-2 ml-1">* Kosongkan semua pilihan jika soal berlaku untuk <b>Semua Kelas</b>.</p>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editBankSoal(data) {
    document.getElementById('edit-kode').value = data.kode_bank;
    document.getElementById('edit-mapel').value = data.mata_pelajaran;
    
    // Reset semua checkbox kelas
    document.querySelectorAll('.edit-kelas-checkbox').forEach(cb => cb.checked = false);
    
    // Centang checkbox sesuai data
    if (data.kelas) {
        let kelasArray = data.kelas.split(',').map(s => s.trim());
        document.querySelectorAll('.edit-kelas-checkbox').forEach(cb => {
            if (kelasArray.includes(cb.value)) {
                cb.checked = true;
            }
        });
    }
    
    let url = "{{ route('admin.bank_soal.update', ':id') }}".replace(':id', data.id);
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
