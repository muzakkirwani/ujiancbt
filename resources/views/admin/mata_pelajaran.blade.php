@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Manajemen Mata Pelajaran</h1>
            <p class="text-slate-500 font-medium mt-1">Atur data mata pelajaran untuk jadwal ujian.</p>
        </div>
        <div class="w-full md:w-auto">
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Mapel</span>
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
            <ul class="list-disc ml-5 text-sm text-red-700 font-medium">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-50 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Daftar Mata Pelajaran</h2>
            <div class="relative w-full md:w-auto">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="searchInput" placeholder="Cari mapel..." class="w-full md:w-64 pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">No</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Mapel</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($mata_pelajaran_list as $i => $mapel)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5 font-medium text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-8 py-5 font-bold text-slate-800">{{ $mapel->nama_mapel }}</td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick='editMapel(@json($mapel))' 
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.mata_pelajaran.destroy', $mapel->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?')">
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
                        <td colspan="3" class="px-8 py-10 text-center text-slate-400 font-medium italic">Belum ada data mata pelajaran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Add -->
<div id="modal-add" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 transform transition-all">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Tambah Mapel</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('admin.mata_pelajaran.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Mata Pelajaran</label>
                <input type="text" name="nama_mapel" required placeholder="Misal: Matematika"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                <i data-lucide="save" class="w-5 h-5"></i>
                <span>Simpan Mapel</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 transform transition-all">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Edit Mapel</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Mata Pelajaran</label>
                <input type="text" name="nama_mapel" id="edit-nama" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                <span>Simpan Perubahan</span>
            </button>
        </form>
    </div>
</div>

<script>
function editMapel(mapel) {
    document.getElementById('edit-id').value = mapel.id;
    document.getElementById('edit-nama').value = mapel.nama_mapel;
    // Set dynamic action URL
    let url = "{{ route('admin.mata_pelajaran.update', ':id') }}".replace(':id', mapel.id);
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
