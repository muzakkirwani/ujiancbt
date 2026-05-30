@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Data Guru</h1>
            <p class="text-slate-500 font-medium mt-1">Kelola data Guru Pengawas dan Administrator.</p>
        </div>
        <div class="w-full md:w-auto">
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-50 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="flex gap-4 border-b-2 border-transparent overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
                <a href="?role=pengawas" class="pb-4 px-2 font-bold text-sm whitespace-nowrap transition-all {{ $role_filter == 'pengawas' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">Pengawas</a>
                <a href="?role=admin" class="pb-4 px-2 font-bold text-sm whitespace-nowrap transition-all {{ $role_filter == 'admin' ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">Administrator</a>
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <div class="relative hidden md:block">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                    <input type="text" id="searchInput" placeholder="Cari nama..." class="w-full md:w-64 pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-4 md:px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Pengguna</th>
                        <th class="px-4 md:px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Username</th>
                        <th class="px-4 md:px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Role</th>
                        <th class="px-4 md:px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($users_list as $u)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-4 md:px-8 py-5">
                            <div class="flex items-center gap-2 md:gap-3">
                                <div class="h-8 w-8 md:h-10 md:w-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 shrink-0">
                                    <i data-lucide="user" class="w-4 h-4 md:w-5 md:h-5"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-slate-800 text-sm line-clamp-1">{{ $u->nama }}</span>
                                    @if($u->mata_pelajaran)
                                        <div class="flex items-center gap-1 mt-1 text-[10px] font-bold text-indigo-600 bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-md w-fit">
                                            <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                                            <span>{{ $u->mata_pelajaran }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 md:px-8 py-5 font-medium text-slate-600 hidden md:table-cell">{{ $u->username }}</td>
                        <td class="px-4 md:px-8 py-5">
                            <span class="px-2 md:px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] md:text-[10px] font-black uppercase tracking-widest border border-slate-200">
                                {{ $u->role }}
                            </span>
                        </td>
                        <td class="px-4 md:px-8 py-5 text-right">
                            <div class="flex justify-end gap-1 md:gap-2">
                                <button onclick='editUser(@json($u))' 
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-slate-400 font-medium italic">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 transform transition-all max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center mb-6 shrink-0">
            <h3 class="text-2xl font-bold text-slate-800">Tambah Pengguna</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST" class="flex-1 flex flex-col min-h-0">
            @csrf
            <div class="space-y-6 overflow-y-auto pr-2 flex-1 pb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" required
                            class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                            placeholder="Nama Lengkap">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">NIP (Opsional)</label>
                        <input type="text" name="nip"
                            class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                            placeholder="NIP">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Role</label>
                    <select name="role" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="pengawas" {{ $role_filter == 'pengawas' ? 'selected' : '' }}>Pengawas</option>
                        <option value="admin" {{ $role_filter == 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Mata Pelajaran (Khusus Pengawas - Bisa Pilih Lebih Dari Satu)</label>
                    <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-2">
                        @foreach($subjects as $subj)
                            <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-xl transition-all">
                                <input type="checkbox" name="mata_pelajaran[]" value="{{ $subj->nama_mapel }}"
                                    class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-700">{{ $subj->nama_mapel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Username</label>
                        <input type="text" name="username" required
                            class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                            placeholder="Username">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Password</label>
                        <input type="text" name="password" required
                            class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                            placeholder="Password">
                    </div>
                </div>
            </div>
            <div class="pt-4 shrink-0 border-t border-slate-100">
                <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    <span>Simpan Pengguna</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 transform transition-all max-h-[90vh] flex flex-col">
        <div class="flex justify-between items-center mb-6 shrink-0">
            <h3 class="text-2xl font-bold text-slate-800">Edit Pengguna</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" class="flex-1 flex flex-col min-h-0">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div class="space-y-6 overflow-y-auto pr-2 flex-1 pb-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit-nama" required
                            class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                            placeholder="Nama Lengkap">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">NIP (Opsional)</label>
                        <input type="text" name="nip" id="edit-nip"
                            class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                            placeholder="NIP">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Role</label>
                    <select name="role" id="edit-role" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="pengawas">Pengawas</option>
                        <option value="admin">Administrator</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Mata Pelajaran (Khusus Pengawas - Bisa Pilih Lebih Dari Satu)</label>
                    <div class="max-h-40 overflow-y-auto border border-slate-200 rounded-2xl p-4 bg-slate-50 space-y-2">
                        @foreach($subjects as $subj)
                            <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-white rounded-xl transition-all">
                                <input type="checkbox" name="mata_pelajaran[]" value="{{ $subj->nama_mapel }}"
                                    class="edit-subject-checkbox w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-xs font-bold text-slate-700">{{ $subj->nama_mapel }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Username</label>
                    <input type="text" name="username" id="edit-username" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                        placeholder="Username">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1 text-slate-400">Password (Kosongkan jika tidak diganti)</label>
                    <input type="text" name="password"
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium"
                        placeholder="Password Baru">
                </div>
            </div>
            <div class="pt-4 shrink-0 border-t border-slate-100">
                <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function editUser(data) {
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-nama').value = data.nama;
    document.getElementById('edit-nip').value = data.nip || '';
    document.getElementById('edit-username').value = data.username;
    document.getElementById('edit-role').value = data.role;
    
    // Reset all checkboxes first
    const checkboxes = document.querySelectorAll('.edit-subject-checkbox');
    checkboxes.forEach(cb => cb.checked = false);
    
    // Check elements that match the user's subjects
    if (data.mata_pelajaran) {
        const userSubjects = data.mata_pelajaran.split(',').map(s => s.trim());
        checkboxes.forEach(cb => {
            if (userSubjects.includes(cb.value)) {
                cb.checked = true;
            }
        });
    }
    
    let url = "{{ route('admin.users.update', ':id') }}".replace(':id', data.id);
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
