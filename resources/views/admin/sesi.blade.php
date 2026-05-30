@extends('layouts.admin')

@section('title', 'Sesi Ujian')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-10 min-h-screen">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Sesi Pelaksanaan</h1>
            <p class="text-slate-500 font-medium mt-1">Atur pembagian jam dan sesi pengerjaan ujian.</p>
        </div>
        <div class="w-full md:w-auto">
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-100 transition-all flex items-center justify-center gap-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Sesi</span>
            </button>
        </div>
    </header>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500"></i>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 md:p-8 border-b border-slate-50 flex justify-between items-center">
            <h2 class="text-xl font-bold text-slate-800">Daftar Sesi</h2>
            <div class="relative w-full md:w-auto">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="searchInput" placeholder="Cari sesi..." class="w-full md:w-64 pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">No</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Nama Sesi</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Jam Mulai</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Jam Berakhir</th>
                        <th class="px-8 py-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($sesi_list as $i => $s)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-5 font-medium text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-8 py-5 font-bold text-slate-800">{{ $s->nama_sesi }}</td>
                        <td class="px-8 py-5 text-slate-600 font-semibold">{{ date('H:i', strtotime($s->jam_mulai)) }}</td>
                        <td class="px-8 py-5 text-slate-600 font-semibold">{{ date('H:i', strtotime($s->jam_berakhir)) }}</td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick='editSesi(@json($s))' 
                                    class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('admin.sesi.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus sesi ini?')">
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
                        <td colspan="5" class="px-8 py-10 text-center text-slate-400 font-medium italic">Belum ada data sesi.</td>
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
            <h3 class="text-2xl font-bold text-slate-800">Tambah Sesi</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form action="{{ route('admin.sesi.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Sesi</label>
                <input type="text" name="nama_sesi" required placeholder="Misal: Sesi 1"
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Jam Mulai</label>
                    <input type="time" name="jam_mulai" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Jam Berakhir</label>
                    <input type="time" name="jam_berakhir" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                <i data-lucide="save" class="w-5 h-5"></i>
                <span>Simpan Sesi</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 transform transition-all">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-2xl font-bold text-slate-800">Edit Sesi</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Sesi</label>
                <input type="text" name="nama_sesi" id="edit-nama" required
                    class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Jam Mulai</label>
                    <input type="time" name="jam_mulai" id="edit-mulai" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Jam Berakhir</label>
                    <input type="time" name="jam_berakhir" id="edit-berakhir" required
                        class="block w-full px-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-lg shadow-indigo-100 transition-all flex justify-center items-center gap-2">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                <span>Simpan Perubahan</span>
            </button>
        </form>
    </div>
</div>

<script>
function editSesi(sesi) {
    document.getElementById('edit-id').value = sesi.id;
    document.getElementById('edit-nama').value = sesi.nama_sesi;
    document.getElementById('edit-mulai').value = sesi.jam_mulai;
    document.getElementById('edit-berakhir').value = sesi.jam_berakhir;
    
    let url = "{{ route('admin.sesi.update', ':id') }}".replace(':id', sesi.id);
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}
</script>
@endsection
