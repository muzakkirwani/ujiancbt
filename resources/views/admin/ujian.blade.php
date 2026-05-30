@extends('layouts.admin')

@section('title', 'Jadwal Ujian')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Jadwal Ujian</h1>
            <p class="text-slate-500 font-medium mt-1">Buat jadwal, tautan soal, dan pantau token ujian aktif.</p>
        </div>
        <div class="w-full md:w-auto">
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Ujian</span>
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

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-xl font-bold text-slate-800">Daftar Jadwal</h2>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="searchInput" placeholder="Cari mata pelajaran..."
                        oninput="filterMapel()"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
                <button onclick="toggleAll(true)" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 whitespace-nowrap">Buka Semua</button>
                <span class="text-slate-300">|</span>
                <button onclick="toggleAll(false)" class="text-xs font-bold text-slate-400 hover:text-slate-600 whitespace-nowrap">Tutup Semua</button>
            </div>
        </div>

        @php $ujianByMapel = $ujian_list->groupBy('mapel'); @endphp

        @if($ujianByMapel->isEmpty())
        <div class="px-8 py-12 text-center text-slate-400 font-medium italic">Belum ada data jadwal ujian.</div>
        @else
        <div class="divide-y divide-slate-50 p-4 md:p-6 space-y-3" id="mapelContainer">
            @foreach ($ujianByMapel as $mapel => $jadwalList)
            @php $folderId = 'folder-' . Str::slug($mapel); @endphp
            <div class="mapel-folder border border-slate-100 rounded-2xl overflow-hidden shadow-sm" data-mapel="{{ strtolower($mapel) }}">

                {{-- Folder Header / Toggle --}}
                <button onclick="toggleFolder('{{ $folderId }}')"
                    class="w-full flex items-center gap-4 px-5 py-4 bg-slate-50 hover:bg-indigo-50 transition-colors text-left group">
                    <div class="w-9 h-9 bg-indigo-600 rounded-xl flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                        <i data-lucide="book-open" class="w-4 h-4 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-slate-800 text-sm">{{ $mapel }}</p>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $jadwalList->count() }} jadwal ujian</p>
                    </div>
                    <span class="bg-indigo-100 text-indigo-600 text-xs font-black px-3 py-1 rounded-full">
                        {{ $jadwalList->count() }}
                    </span>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400 transition-transform folder-chevron" id="chevron-{{ $folderId }}"></i>
                </button>

                {{-- Folder Content --}}
                <div id="{{ $folderId }}" class="hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-indigo-50/50 text-[10px] font-black text-slate-400 uppercase tracking-wider">
                                <th class="px-6 py-3">Tanggal & Sesi</th>
                                <th class="px-6 py-3">Kelas</th>
                                <th class="px-6 py-3">Link Ujian</th>
                                <th class="px-6 py-3">Token</th>
                                <th class="px-6 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm">
                            @foreach ($jadwalList as $u)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-700">{{ date('d M Y', strtotime($u->tanggal)) }}</div>
                                    <div class="text-[10px] text-indigo-500 font-bold uppercase tracking-wide mt-0.5">{{ $u->sesi->nama_sesi ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-full text-xs font-bold">
                                        {{ $u->kelas->nama_kelas ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($u->jenis_ujian == 'googleform')
                                        <a href="{{ $u->link_ujian }}" target="_blank"
                                            class="text-xs text-indigo-600 font-bold hover:underline inline-flex items-center gap-1">
                                            <span>Buka Link</span>
                                            <i data-lucide="external-link" class="w-3 h-3"></i>
                                        </a>
                                        <div class="text-[10px] text-slate-400 mt-0.5">Google Form</div>
                                    @else
                                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">Pilihan Ganda</span>
                                        <div class="text-[10px] text-slate-500 mt-1 truncate max-w-[150px]" title="{{ $u->bankSoal->mata_pelajaran ?? 'Bank Soal Dihapus' }}">
                                            {{ $u->bankSoal->kode_bank ?? '-' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold bg-slate-50 text-slate-700 border border-slate-200 px-2.5 py-1 rounded-lg uppercase tracking-widest text-xs">
                                            {{ $u->token }}
                                        </span>
                                        <form action="{{ route('admin.ujian.generate_token', $u->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="p-1 text-slate-400 hover:text-indigo-600 transition-colors" title="Acak Token">
                                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick='editUjian(@json($u))'
                                            class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                                        </button>
                                        <form action="{{ route('admin.ujian.destroy', $u->id) }}" method="POST"
                                              class="inline" onsubmit="return confirm('Hapus jadwal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</main>


<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 transform transition-all my-8 max-h-[calc(100vh-4rem)] overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Tambah Jadwal</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('admin.ujian.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Mata Pelajaran</label>
                <input type="text" name="mapel" required placeholder="Misal: Matematika Wajib"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tanggal Ujian</label>
                    <input type="date" name="tanggal" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Sesi Ujian</label>
                    <select name="sesi_id" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="">Pilih Sesi</option>
                        @foreach ($sesi_list as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_sesi }} ({{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_berakhir)) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Pilih Kelas (Bisa lebih dari satu)</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-48 overflow-y-auto p-1">
                    @foreach ($kelas_list as $k)
                    <label class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:bg-indigo-50 transition-colors">
                        <input type="checkbox" name="kelas_id[]" value="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}" class="add-kelas-checkbox w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500" onchange="filterBankSoal('add')">
                        <span class="text-sm font-bold text-slate-700">{{ $k->nama_kelas }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tipe Ujian</label>
                <select name="jenis_ujian" id="add-jenis-ujian" onchange="toggleAddJenisUjian()" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="googleform">Google Form (Eksternal Link)</option>
                    <option value="pilihan_ganda">Pilihan Ganda (Bank Soal)</option>
                </select>
            </div>
            
            <div id="add-container-link">
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tautan Soal (Google Form / DLL)</label>
                <input type="url" name="link_ujian" id="add-link-ujian" placeholder="https://docs.google.com/forms/..."
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>

            <div id="add-container-banksoal" class="hidden">
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Pilih Bank Soal</label>
                <select name="bank_soal_id" id="add-bank-soal"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="">Pilih Bank Soal</option>
                    @foreach ($bank_soal_list as $b)
                        <option value="{{ $b->id }}" data-kelas="{{ $b->kelas }}">{{ $b->kode_bank }} - {{ $b->mata_pelajaran }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                <i data-lucide="save" class="w-5 h-5"></i>
                <span>Simpan Ujian</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl p-10 transform transition-all my-8 max-h-[calc(100vh-4rem)] overflow-y-auto">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Edit Jadwal</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Mata Pelajaran</label>
                <input type="text" name="mapel" id="edit-mapel" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tanggal Ujian</label>
                    <input type="date" name="tanggal" id="edit-tanggal" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Sesi Ujian</label>
                    <select name="sesi_id" id="edit-sesi" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        @foreach ($sesi_list as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_sesi }} ({{ date('H:i', strtotime($s->jam_mulai)) }} - {{ date('H:i', strtotime($s->jam_berakhir)) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Kelas Peserta</label>
                <select name="kelas_id" id="edit-kelas" onchange="filterBankSoal('edit')" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelas_list as $k)
                        <option value="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tipe Ujian</label>
                <select name="jenis_ujian" id="edit-jenis-ujian" onchange="toggleEditJenisUjian()" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="googleform">Google Form (Eksternal Link)</option>
                    <option value="pilihan_ganda">Pilihan Ganda (Bank Soal)</option>
                </select>
            </div>
            <div id="edit-container-link">
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tautan Soal (Google Form / DLL)</label>
                <input type="url" name="link_ujian" id="edit-link"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div id="edit-container-banksoal" class="hidden">
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Pilih Bank Soal</label>
                <select name="bank_soal_id" id="edit-bank-soal"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="">Pilih Bank Soal</option>
                    @foreach ($bank_soal_list as $b)
                        <option value="{{ $b->id }}" data-kelas="{{ $b->kelas }}">{{ $b->kode_bank }} - {{ $b->mata_pelajaran }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                <span>Simpan Perubahan</span>
            </button>
        </form>
    </div>
</div>

<script>
function editUjian(data) {
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-mapel').value = data.mapel;
    document.getElementById('edit-tanggal').value = data.tanggal;
    document.getElementById('edit-sesi').value = data.sesi_id;
    document.getElementById('edit-kelas').value = data.kelas_id;
    
    // Trigger filter before setting bank soal value
    filterBankSoal('edit');

    document.getElementById('edit-jenis-ujian').value = data.jenis_ujian || 'googleform';
    toggleEditJenisUjian();

    document.getElementById('edit-link').value = data.link_ujian || '';
    document.getElementById('edit-bank-soal').value = data.bank_soal_id || '';
    
    let url = "{{ route('admin.ujian.update', ':id') }}".replace(':id', data.id);
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}

function filterBankSoal(type) {
    let selectedClasses = [];
    if (type === 'add') {
        document.querySelectorAll('.add-kelas-checkbox:checked').forEach(cb => {
            selectedClasses.push(cb.getAttribute('data-nama'));
        });
    } else {
        const kelasSelect = document.getElementById(type + '-kelas');
        if (kelasSelect) {
            const selectedOption = kelasSelect.options[kelasSelect.selectedIndex];
            if(selectedOption) selectedClasses.push(selectedOption.getAttribute('data-nama'));
        }
    }
    
    const bankSoalSelect = document.getElementById(type + '-bank-soal');
    const options = bankSoalSelect.querySelectorAll('option');
    
    options.forEach(opt => {
        if (opt.value === "") return; // Skip placeholder
        const optKelas = opt.getAttribute('data-kelas');
        
        // Tampilkan jika Bank Soal tidak punya kelas (Bebas/Umum)
        if (!optKelas) {
            opt.hidden = false;
            opt.disabled = false;
        } else {
            let bankClasses = optKelas.split(',').map(s => s.trim());
            let match = false;
            
            // Jika tidak ada kelas yang dipilih, tampilkan semua opsi
            if (selectedClasses.length === 0) {
                match = true;
            } else {
                for(let c of bankClasses) {
                    if(selectedClasses.includes(c)) { 
                        match = true; 
                        break; 
                    }
                }
            }
            
            if(match) {
               opt.hidden = false;
               opt.disabled = false;
            } else {
               opt.hidden = true;
               opt.disabled = true;
               if(opt.selected) opt.selected = false;
            }
        }
    });
}

function toggleAddJenisUjian() {
    let jenis = document.getElementById('add-jenis-ujian').value;
    if (jenis === 'googleform') {
        document.getElementById('add-container-link').classList.remove('hidden');
        document.getElementById('add-container-banksoal').classList.add('hidden');
        document.getElementById('add-link-ujian').setAttribute('required', 'required');
        document.getElementById('add-bank-soal').removeAttribute('required');
    } else {
        document.getElementById('add-container-link').classList.add('hidden');
        document.getElementById('add-container-banksoal').classList.remove('hidden');
        document.getElementById('add-link-ujian').removeAttribute('required');
        document.getElementById('add-bank-soal').setAttribute('required', 'required');
    }
}

function toggleEditJenisUjian() {
    let jenis = document.getElementById('edit-jenis-ujian').value;
    if (jenis === 'googleform') {
        document.getElementById('edit-container-link').classList.remove('hidden');
        document.getElementById('edit-container-banksoal').classList.add('hidden');
        document.getElementById('edit-link').setAttribute('required', 'required');
        document.getElementById('edit-bank-soal').removeAttribute('required');
    } else {
        document.getElementById('edit-container-link').classList.add('hidden');
        document.getElementById('edit-container-banksoal').classList.remove('hidden');
        document.getElementById('edit-link').removeAttribute('required');
        document.getElementById('edit-bank-soal').setAttribute('required', 'required');
    }
}

function toggleFolder(id) {
    const content = document.getElementById(id);
    const chevron = document.getElementById('chevron-' + id);
    const isHidden = content.classList.contains('hidden');
    content.classList.toggle('hidden', !isHidden);
    if (chevron) chevron.style.transform = isHidden ? 'rotate(180deg)' : '';
}

function toggleAll(open) {
    document.querySelectorAll('.mapel-folder [id^="folder-"]').forEach(el => {
        el.classList.toggle('hidden', !open);
        const chevron = document.getElementById('chevron-' + el.id);
        if (chevron) chevron.style.transform = open ? 'rotate(180deg)' : '';
    });
}

function filterMapel() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('.mapel-folder').forEach(folder => {
        const name = folder.dataset.mapel || '';
        const match = name.includes(query);
        folder.style.display = match ? '' : 'none';
        if (match && query) {
            // Auto-open matched folders when searching
            const content = folder.querySelector('[id^="folder-"]');
            if (content && content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                const chevron = document.getElementById('chevron-' + content.id);
                if (chevron) chevron.style.transform = 'rotate(180deg)';
            }
        }
    });
}
</script>
@endsection
