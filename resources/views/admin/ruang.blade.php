@extends('layouts.admin')

@section('title', 'Manajemen Ruang')

@section('content')
<main class="ml-0 md:ml-72 p-4 md:p-6 min-h-screen text-slate-800">
    <!-- Header -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">Manajemen Ruang & Plotting</h1>
            <p class="text-slate-500 text-xs font-medium mt-0.5">Atur alokasi ruang ujian, pembagian siswa, dan pengawas.</p>
        </div>
        <div class="flex flex-wrap gap-2 w-full md:w-auto">
            <button onclick="document.getElementById('modal-plot-siswa').classList.remove('hidden')" 
                class="bg-indigo-50 text-indigo-600 border-2 border-indigo-100 px-3.5 py-2 rounded-xl font-bold transition-all flex items-center justify-center gap-2 hover:bg-indigo-600 hover:text-white flex-1 md:flex-none text-xs">
                <i data-lucide="users-2" class="w-3.5 h-3.5"></i>
                <span>Plot Siswa</span>
            </button>
            <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl font-bold shadow transition-all flex items-center justify-center gap-2 text-xs">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                <span>Tambah Ruangan</span>
            </button>
        </div>
    </header>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-6 rounded-xl">
            <div class="flex items-center gap-2.5 mb-1.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-500"></i>
                <p class="text-xs text-red-700 font-bold">Terjadi Kesalahan Pengisian:</p>
            </div>
            <ul class="list-disc list-inside text-[10px] text-red-600 font-medium space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-6 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-4 h-4 text-green-500"></i>
            <p class="text-xs text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 md:p-5 border-b border-slate-50 flex justify-between items-center">
            <h2 class="text-base font-bold text-slate-800">Daftar Ruangan</h2>
            <div class="relative w-full md:w-auto">
                <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" id="searchInput" placeholder="Cari ruangan..." class="w-full md:w-60 pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left font-medium">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Ruangan</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kapasitas Plot</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pengawas Aktif</th>
                        <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-slate-700">
                    @forelse ($ruang_list as $r)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 py-2.5 font-bold text-sm text-slate-800 leading-tight">{{ $r->nama_ruang }}</td>
                        <td class="px-4 py-2.5">
                            <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-lg text-[10px] font-bold">{{ $r->siswa_count ?? 0 }} Siswa</span>
                        </td>
                        <td class="px-4 py-2.5">
                            @if ($r->pengawas_count > 0)
                                <span class="bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-lg text-[10px] font-bold">{{ $r->pengawas_count }} Pengawas Terplot</span>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Belum terplot</span>
                            @endif
                        </td>
                        <td class="px-4 py-2.5 text-right">
                            <div class="flex justify-end items-center gap-1.5">
                                <button onclick='plotPengawas(@json($r))'
                                    class="text-[10px] font-bold bg-indigo-50 text-indigo-600 px-2.5 py-1.5 rounded-lg hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Plot Pengawas">
                                    + Pengawas
                                </button>
                                <a href="{{ route('admin.ruang.cetak_kartu', $r->id) }}" target="_blank"
                                    class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors" title="Cetak Kartu Ujian">
                                    <i data-lucide="contact" class="w-3.5 h-3.5"></i>
                                </a>
                                <a href="{{ route('admin.ruang.cetak_daftar_hadir', $r->id) }}" target="_blank"
                                    class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors" title="Cetak Daftar Hadir">
                                    <i data-lucide="clipboard-list" class="w-3.5 h-3.5"></i>
                                </a>
                                <button onclick='editRuang(@json($r))' 
                                    class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </button>
                                <form action="{{ route('admin.ruang.destroy', $r->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus ruangan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400 text-xs font-semibold italic">Belum ada data ruangan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Daftar Tugas Pengawas (grouped by room) ────── --}}
    <div class="mt-6">
        <div class="flex justify-between items-center mb-3">
            <h2 class="text-base font-bold text-slate-800">Daftar Tugas Pengawas</h2>
        </div>

        @php $tugasByRuang = $tugas->groupBy(fn($t) => $t->ruang->nama_ruang ?? 'Tanpa Ruang'); @endphp

        @if($tugasByRuang->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 px-4 py-8 text-center text-slate-400 text-xs font-semibold italic">
            Belum ada tugas pengawas.
        </div>
        @else
        <div class="space-y-4">
            @foreach ($tugasByRuang as $namaRuang => $tugasGroup)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                {{-- Room header --}}
                <div class="px-4 md:px-5 py-3 bg-indigo-50/70 border-b border-indigo-100/50 flex items-center gap-2.5">
                    <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center shrink-0">
                        <i data-lucide="door-open" class="w-3.5 h-3.5 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-slate-800 text-xs">{{ $namaRuang }}</h3>
                    </div>
                    <span class="bg-indigo-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                        {{ $tugasGroup->count() }} Pengawas
                    </span>
                </div>

                {{-- Tugas table for this room --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left font-medium">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-4 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Pengawas</th>
                                <th class="px-4 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sesi</th>
                                <th class="px-4 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-2.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700">
                            @foreach ($tugasGroup as $t)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-4 py-2.5 font-bold text-sm text-slate-800 leading-tight">{{ $t->user->nama ?? '-' }}</td>
                                <td class="px-4 py-2.5">
                                    <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-lg text-[10px] font-bold">
                                        {{ $t->sesi->nama_sesi ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-xs text-slate-600">{{ date('d/m/Y', strtotime($t->tanggal)) }}</td>
                                <td class="px-4 py-2.5 text-right">
                                    <div class="flex justify-end items-center gap-1.5">
                                        <button onclick='editTugas(@json($t))'
                                            class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <form action="{{ route('admin.ruang.tugas.destroy', $t->id) }}" method="POST"
                                              class="inline" onsubmit="return confirm('Hapus tugas pengawas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
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
<div id="modal-add" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Tambah Ruangan</h3>
            <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('admin.ruang.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Nama Ruangan</label>
                <input type="text" name="nama_ruang" required placeholder="Misal: Laboratorium Komputer 1"
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Ruangan</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="modal-edit" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Edit Ruangan</h3>
            <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="edit-id">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Nama Ruangan</label>
                <input type="text" name="nama_ruang" id="edit-nama" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                <span>Simpan Perubahan</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Plot Siswa -->
<div id="modal-plot-siswa" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Plot Siswa Bulk</h3>
            <button onclick="document.getElementById('modal-plot-siswa').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('admin.ruang.plot_siswa') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Pilih Kelas</label>
                <select name="kelas_id" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelas_list as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Pilih Ruangan</label>
                <select name="ruang_id" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="">Pilih Ruangan</option>
                    @foreach ($ruang_list as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_ruang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Prefix Nomor Ujian</label>
                <input type="text" name="prefix" value="UJN-" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Mulai Plotting</span>
            </button>
        </form>
    </div>
</div>

<!-- Modal Plot Pengawas -->
<div id="modal-plot-pengawas" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Plot Pengawas Ujian</h3>
            <button onclick="document.getElementById('modal-plot-pengawas').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('admin.ruang.plot_pengawas') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="ruang_id" id="plot-ruang-id">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Ruangan</label>
                <input type="text" id="plot-ruang-nama" disabled
                    class="block w-full px-4 py-2 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-500 outline-none font-medium">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Pilih Pengawas</label>
                <select name="user_id" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    <option value="">Pilih Pengawas</option>
                    @foreach ($pengawas_list as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Sesi Ujian</label>
                    <select name="sesi_id" required
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                        <option value="">Pilih Sesi</option>
                        @foreach ($sesi_list as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_sesi }} ({{ date('H:i', strtotime($s->jam_mulai)) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Tanggal</label>
                    <input type="date" name="tanggal" required
                        class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
                </div>
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Simpan Pengawas</span>
            </button>
        </form>
    </div>
</div>

<script>
function editRuang(data) {
    document.getElementById('edit-id').value = data.id;
    document.getElementById('edit-nama').value = data.nama_ruang;
    
    let url = "{{ route('admin.ruang.update', ':id') }}".replace(':id', data.id);
    document.getElementById('edit-form').action = url;
    document.getElementById('modal-edit').classList.remove('hidden');
}

function plotPengawas(data) {
    document.getElementById('plot-ruang-id').value = data.id;
    document.getElementById('plot-ruang-nama').value = data.nama_ruang;
    document.getElementById('modal-plot-pengawas').classList.remove('hidden');
}

function editTugas(data) {
    let url = "{{ route('admin.ruang.tugas.update', ':id') }}".replace(':id', data.id);
    document.getElementById('edit-tugas-form').action = url;
    document.getElementById('edit-tugas-user').value   = data.user_id;
    document.getElementById('edit-tugas-ruang').value  = data.ruang_id;
    document.getElementById('edit-tugas-sesi').value   = data.sesi_id;
    document.getElementById('edit-tugas-tanggal').value = data.tanggal;
    document.getElementById('modal-edit-tugas').classList.remove('hidden');
}
</script>

<!-- Modal Edit Tugas Pengawas -->
<div id="modal-edit-tugas" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl p-6 transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-slate-800">Edit Tugas Pengawas</h3>
            <button onclick="document.getElementById('modal-edit-tugas').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="edit-tugas-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Pengawas</label>
                <select id="edit-tugas-user" name="user_id" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    @foreach ($pengawas_list as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Ruangan</label>
                <select id="edit-tugas-ruang" name="ruang_id" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    @foreach ($ruang_list as $r)
                        <option value="{{ $r->id }}">{{ $r->nama_ruang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Sesi Ujian</label>
                <select id="edit-tugas-sesi" name="sesi_id" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium appearance-none">
                    @foreach ($sesi_list as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_sesi }} ({{ date('H:i', strtotime($s->jam_mulai)) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 ml-0.5">Tanggal</label>
                <input type="date" id="edit-tugas-tanggal" name="tanggal" required
                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:ring-2 focus:ring-indigo-500 outline-none transition-all font-medium">
            </div>
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-xl shadow transition-all flex justify-center items-center gap-2 text-xs">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Simpan Perubahan</span>
            </button>
        </form>
    </div>
</div>

@endsection
