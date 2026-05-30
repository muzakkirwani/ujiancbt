@extends('layouts.admin')

@section('title', 'Data Siswa')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-6 min-h-screen text-slate-800">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">Data Siswa</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">Kelola data seluruh siswa yang terdaftar.</p>
        </div>
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <button onclick="document.getElementById('modal-import').classList.remove('hidden')" 
                class="bg-emerald-50 text-emerald-600 border-2 border-emerald-100 px-3.5 py-2 rounded-xl font-bold transition-all flex items-center justify-center gap-2 hover:bg-emerald-600 hover:text-white flex-1 md:flex-none text-xs">
                <i data-lucide="upload" class="w-3.5 h-3.5"></i>
                <span>Import</span>
            </button>
            <a href="{{ route('admin.siswa.export') }}"
                class="bg-amber-50 text-amber-600 border-2 border-amber-100 px-3.5 py-2 rounded-xl font-bold transition-all flex items-center justify-center gap-2 hover:bg-amber-600 hover:text-white flex-1 md:flex-none text-xs">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Export</span>
            </a>
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold shadow transition-all flex items-center justify-center gap-2 text-xs">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Tambah Siswa</span>
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            <p class="text-xs text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-base font-bold text-slate-800 font-bold">Daftar Siswa per Kelas</h2>
        <div class="relative hidden md:block">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="searchInput" placeholder="Cari siswa..." 
                class="pl-9 pr-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 w-60 font-medium text-slate-600 shadow-sm outline-none">
        </div>
    </div>

    <!-- Folders Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="kelasContainer">
        @forelse ($kelas_list as $k)
        @php
            $siswa_kelas = $users_list->where('kelas_id', $k->id);
        @endphp
        <div class="kelas-card bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden group transition-all duration-300 hover:shadow-md hover:border-indigo-100" data-kelas="{{ $k->nama_kelas }}">
            {{-- Folder Header Button --}}
            <button type="button" onclick="openFolderModal('{{ $k->id }}')" class="w-full px-4 py-4 bg-gradient-to-r from-slate-50/50 to-white flex items-center justify-between hover:from-indigo-50/40 transition-all text-left">
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 bg-indigo-50 group-hover:bg-indigo-600 rounded-xl flex items-center justify-center shrink-0 transition-colors">
                        <i data-lucide="folder" class="w-5 h-5 text-indigo-600 group-hover:text-white transition-colors"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">{{ $k->nama_kelas }}</h3>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $siswa_kelas->count() }} Siswa</p>
                    </div>
                </div>
                <div class="bg-white shadow-sm border border-slate-50 p-1.5 rounded-lg shrink-0">
                    <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition-transform"></i>
                </div>
            </button>

            {{-- Folder Modal Popup --}}
            <div id="modal-folder-{{ $k->id }}" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
                <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl p-5 transform transition-all my-8 max-h-[calc(100vh-4rem)] flex flex-col">
                    <!-- Modal Header -->
                    <div class="flex justify-between items-center pb-3.5 border-b border-slate-100 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center shadow-inner">
                                <i data-lucide="folder-open" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-800 leading-tight">Daftar Siswa Kelas {{ $k->nama_kelas }}</h3>
                                <p class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $siswa_kelas->count() }} Siswa terdaftar</p>
                            </div>
                        </div>
                        <button type="button" onclick="closeFolderModal('{{ $k->id }}')" class="p-1.5 hover:bg-slate-50 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Modal Body (Student List Grid inside popup) -->
                    <div class="overflow-y-auto flex-1 mt-4">
                        <table class="w-full text-left font-medium">
                            <thead>
                                <tr class="bg-slate-50/50">
                                    <th class="px-4 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa</th>
                                    <th class="px-4 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">User / Password</th>
                                    <th class="px-4 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-slate-700">
                                @forelse ($siswa_kelas as $u)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-4 py-2">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-indigo-50 flex items-center justify-center overflow-hidden border border-slate-100 shadow-sm shrink-0">
                                                @if($u->foto)
                                                    <img src="{{ asset('assets/uploads/users/' . $u->foto) }}" class="w-full h-full object-cover">
                                                @else
                                                    <i data-lucide="user" class="w-4 h-4 text-indigo-200"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800 leading-tight">{{ $u->nama }}</p>
                                                <p class="text-[9px] text-slate-400 font-semibold mt-0.5">NISN: {{ $u->nisn ?: '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="text-slate-400 font-semibold">User:</span>
                                                <span class="font-mono font-bold text-slate-700 text-xs">{{ $u->username }}</span>
                                            </div>
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="text-slate-400 font-semibold">Pass:</span>
                                                <span class="font-mono text-slate-400 italic text-xs">{{ $u->password_view ?? '********' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <div class="flex justify-end gap-1.5">
                                            <a href="{{ route('admin.siswa.cetak_kartu', $u->id) }}" target="_blank"
                                                class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm shadow-emerald-100" title="Cetak Kartu">
                                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                            </a>
                                            <button type="button" onclick='editSiswaFromModal(@json($u), "{{ $k->id }}")' 
                                                class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm shadow-indigo-100" title="Edit">
                                                <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                            <form action="{{ route('admin.siswa.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus siswa ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm shadow-red-100" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="3" class="px-4 py-6 text-center text-slate-400 text-xs font-semibold italic">Belum ada siswa di kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-slate-100 px-4 py-8 text-center text-slate-400 text-xs font-semibold italic">
            Belum ada data kelas. Silakan tambahkan kelas terlebih dahulu.
        </div>
        @endforelse
    </div>
</main>

<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all my-8 max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Tambah Siswa</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">NISN</label>
                <input type="text" name="nisn" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                    placeholder="NISN">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Nama Lengkap</label>
                <input type="text" name="nama" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                    placeholder="Nama Lengkap">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir"
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                        placeholder="Tempat Lahir">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Kelas</label>
                    <select name="kelas_id" required
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="">Pilih Kelas</option>
                        @foreach ($kelas_list as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Username</label>
                    <input type="text" name="username" required
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                        placeholder="Username">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Foto Siswa</label>
                <input type="file" name="foto" accept="image/*"
                    class="block w-full px-4 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Password</label>
                <input type="text" name="password" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                    placeholder="Password">
            </div>
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Siswa</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all my-8 max-h-[calc(100vh-2rem)] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Edit Siswa</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">NISN</label>
                <input type="text" name="nisn" id="edit-nisn" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                    placeholder="NISN">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Nama Lengkap</label>
                <input type="text" name="nama" id="edit-nama" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                    placeholder="Nama Lengkap">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="edit-tempat"
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                        placeholder="Tempat Lahir">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="edit-tanggal"
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Kelas</label>
                    <select name="kelas_id" id="edit-kelas" required
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="">Pilih Kelas</option>
                        @foreach ($kelas_list as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Username</label>
                    <input type="text" name="username" id="edit-username" required
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                        placeholder="Username">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Foto Siswa</label>
                <input type="file" name="foto" accept="image/*"
                    class="block w-full px-4 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Password Baru</label>
                <input type="text" name="password" id="edit-password"
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                    placeholder="Biarkan kosong jika tidak diganti">
            </div>
            <div class="pt-2">
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Import -->
<div id="modal-import" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden overflow-y-auto">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all my-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Import Siswa</h3>
            <button onclick="document.getElementById('modal-import').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100 mb-4">
                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest mb-1.5">Petunjuk Import</p>
                <ul class="text-[9px] text-indigo-800 space-y-1.5 font-medium leading-relaxed">
                    <li>1. Gunakan file format CSV (Excel CSV).</li>
                    <li>2. Kolom berurutan: <b>Nama, NISN, Username, Password, Nama Kelas</b>.</li>
                    <li>3. Baris pertama dianggap sebagai Header.</li>
                </ul>
                <a href="{{ route('admin.siswa.download_template') }}" class="inline-block mt-2.5 text-[10px] font-bold text-indigo-600 hover:underline">Download Template CSV</a>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Pilih File CSV</label>
                <input type="file" name="file_siswa" accept=".csv" required
                    class="block w-full px-4 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span>Mulai Import</span>
            </button>
        </form>
    </div>
</div>

<script>
function editSiswa(data) {
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-nisn').value = data.nisn || '';
    document.getElementById('edit-nama').value = data.nama;
    document.getElementById('edit-tempat').value = data.tempat_lahir || '';
    document.getElementById('edit-tanggal').value = data.tanggal_lahir || '';
    document.getElementById('edit-kelas').value = data.kelas_id;
    document.getElementById('edit-username').value = data.username;
    document.getElementById('edit-password').value = data.password_view || '';
    
    let url = "{{ route('admin.siswa.update', ':id') }}".replace(':id', data.id);
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}

function editSiswaFromModal(data, folderId) {
    closeFolderModal(folderId);
    editSiswa(data);
}

function openFolderModal(id) {
    document.getElementById('modal-folder-' + id).classList.remove('hidden');
}

function closeFolderModal(id) {
    document.getElementById('modal-folder-' + id).classList.add('hidden');
}

document.getElementById('searchInput')?.addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.kelas-card').forEach(card => {
        let hasVisibleRow = false;
        const rows = card.querySelectorAll('tbody tr:not(.empty-row)');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(term)) {
                row.style.display = '';
                hasVisibleRow = true;
            } else {
                row.style.display = 'none';
            }
        });
        
        if (term === '') {
            card.style.display = '';
        } else {
            if (hasVisibleRow) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        }
    });
});
</script>
@endsection
