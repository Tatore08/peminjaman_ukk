@extends('layouts.app')

@section('title', 'Data Pengembalian')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Data Pengembalian</h2>
        @if(auth()->user()->level == 'peminjam' && $peminjamanList->count() > 0)
        <button onclick="openModal('tambah')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition">
            <i class="fas fa-plus"></i>
            <span>Ajukan Pengembalian</span>
        </button>
        @endif
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Error Message --}}
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @if(auth()->user()->level != 'peminjam')
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                    @endif
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alat (Kode)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Kembali</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terlambat</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Denda</th>
                    @if(auth()->user()->level == 'admin' || auth()->user()->level == 'petugas')
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pengembalian as $item)
                    <tr class="hover:bg-gray-50 transition">
                        @if(auth()->user()->level != 'peminjam')
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item->peminjaman->user->username }}
                        </td>
                        @endif
                        <td class="px-6 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $item->peminjaman->alat->nama_alat }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $item->peminjaman->alat->kode_alat }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $item->tanggal_kembali_aktual->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->status_pengembalian == 'pending')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Disetujui
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->kondisi_alat == 'baik')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Baik
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Rusak
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if($item->keterlambatan_hari > 0)
                                <span class="text-red-600 font-semibold">{{ $item->keterlambatan_hari }} hari</span>
                            @else
                                <span class="text-green-600">Tepat waktu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($item->getTotalDenda() > 0)
                                <span class="text-red-600">{{ $item->getTotalDendaFormatted() }}</span>
                            @else
                                <span class="text-gray-600">Rp 0</span>
                            @endif
                        </td>
                        @if(auth()->user()->level == 'admin' || auth()->user()->level == 'petugas')
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if($item->status_pengembalian == 'pending')
                                    {{-- Approve --}}
                                    <button onclick="openApproveModal({{ $item->pengembalian_id }}, '{{ $item->peminjaman->alat->nama_alat }}', '{{ $item->peminjaman->alat->kode_alat }}', {{ $item->keterlambatan_hari }}, {{ $item->peminjaman->alat->harga_beli ?? 0 }})" 
                                        class="text-green-600 hover:text-green-900" title="Setujui">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                    {{-- Reject --}}
                                    <form action="{{ route('pengembalian.reject', $item->pengembalian_id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin tolak pengembalian ini?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Tolak">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- TOMBOL PRINT (hanya untuk approved) --}}
                                @if($item->status_pengembalian == 'approved' && auth()->user()->level == 'admin')
                                    <a href="{{ route('laporan.pengembalian', $item->pengembalian_id) }}" 
                                    target="_blank"
                                    class="text-purple-600 hover:text-purple-900" 
                                    title="Cetak Laporan">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @endif

                                @if($item->catatan && $item->status_pengembalian == 'approved')
                                    <button onclick="showCatatan('{{ $item->catatan }}')" class="text-blue-600 hover:text-blue-900" title="Lihat Catatan">
                                        <i class="fas fa-comment-alt"></i>
                                    </button>
                                @endif

                                @if(auth()->user()->level == 'admin')
                                    <form action="{{ route('pengembalian.destroy', $item->pengembalian_id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin hapus pengembalian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-undo text-4xl text-gray-300 mb-2"></i>
                            <p>Belum ada data pengembalian.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($pengembalian->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $pengembalian->links() }}
            </div>
        @endif
    </div>


    {{-- ================================================ --}}
    {{-- MODAL AJUKAN PENGEMBALIAN (Peminjam) --}}
    {{-- ================================================ --}}
    @if(auth()->user()->level == 'peminjam')
    <div id="modalTambah" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Ajukan Pengembalian</h3>
                <button onclick="closeModal('tambah')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @if($peminjamanList->count() > 0)
            <form action="{{ route('pengembalian.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Peminjaman (dropdown) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alat yang Dikembalikan <span class="text-red-500">*</span>
                    </label>
                    <select name="peminjaman_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($peminjamanList as $p)
                            <option value="{{ $p->peminjaman_id }}">
                                {{ $p->alat->nama_alat }} ({{ $p->alat->kode_alat }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-700">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pengembalian akan dicek oleh petugas. Kondisi dan denda akan ditentukan setelah pengecekan fisik.
                    </p>
                </div>

                {{-- Tombol --}}
                <div class="flex space-x-2 pt-2">
                    <button type="submit"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition text-sm">
                        <i class="fas fa-paper-plane mr-1"></i> Ajukan
                    </button>
                    <button type="button" onclick="closeModal('tambah')"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition text-sm">
                        Batal
                    </button>
                </div>
            </form>
            @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                <p>Tidak ada peminjaman yang bisa dikembalikan.</p>
            </div>
            @endif
        </div>
    </div>
    @endif


    {{-- ================================================ --}}
    {{-- MODAL APPROVE PENGEMBALIAN (Petugas/Admin) --}}
    {{-- ================================================ --}}
    @if(auth()->user()->level == 'admin' || auth()->user()->level == 'petugas')
    <div id="modalApprove" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Approve Pengembalian</h3>
                <button onclick="closeModal('approve')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="formApprove" method="POST" class="space-y-4">
                @csrf

                {{-- Info Alat --}}
                <div class="bg-gray-50 p-3 rounded-lg">
                    <div class="text-sm font-medium text-gray-700" id="approve_alat_name"></div>
                    <div class="text-xs text-gray-500 font-mono" id="approve_alat_code"></div>
                </div>

                {{-- Keterlambatan --}}
                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg">
                    <p class="text-xs text-yellow-800">
                        <i class="fas fa-clock mr-1"></i>
                        Keterlambatan: <strong id="approve_keterlambatan">0 hari</strong>
                    </p>
                </div>

                {{-- Kondisi Alat --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Kondisi Alat <span class="text-red-500">*</span>
                    </label>
                    <select name="kondisi_alat" id="kondisiAlat" required onchange="handleKondisiChange()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="baik">Baik - Tidak Ada Kerusakan</option>
                        <option value="rusak">Rusak - Ada Kerusakan</option>
                    </select>
                </div>

                {{-- Persentase Kerusakan (muncul jika rusak) --}}
                <div id="persenKerusakanDiv" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Persentase Kerusakan (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="persen_kerusakan" id="persenKerusakan" min="0" max="100" value="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="0-100" oninput="updateDendaKerusakan()">
                    <p class="text-xs text-gray-500 mt-1">0-100% (denda akan dihitung otomatis berdasarkan harga beli)</p>
                    <div id="persenInfo" class="text-xs text-blue-600 mt-2 hidden"></div>
                </div>

                {{-- Denda Keterlambatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Denda Keterlambatan <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="denda_keterlambatan" id="dendaKeterlambatan" min="0" step="1000" value="0" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                        placeholder="0" onchange="updateTotalDenda()">
                    <p class="text-xs text-gray-500 mt-1">Denda berdasarkan keterlambatan (Rp 5.000/hari)</p>
                </div>

                {{-- Denda Kerusakan (Read-Only) --}}
                <div id="dendaKerusakanDiv" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Denda Kerusakan (Otomatis)
                    </label>
                    <input type="number" name="denda_kerusakan" id="dendaKerusakan" min="0" step="1000" value="0" readonly
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm cursor-not-allowed">
                    <p class="text-xs text-gray-500 mt-1" id="rumusDenda"></p>
                </div>

                {{-- Total Denda (Otomatis) --}}
                <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg">
                    <p class="text-sm font-medium text-blue-900">
                        Total Denda: <span id="totalDendaDisplay" class="text-lg font-bold text-blue-700">Rp 0</span>
                    </p>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Kerusakan</label>
                    <textarea name="catatan" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm resize-none"
                        placeholder="Jelaskan kerusakan jika ada..."></textarea>
                </div>

                {{-- Tombol --}}
                <div class="flex space-x-2 pt-2">
                    <button type="submit"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition text-sm">
                        <i class="fas fa-check mr-1"></i> Setujui
                    </button>
                    <button type="button" onclick="closeModal('approve')"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 rounded-lg transition text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif


    {{-- ================================================ --}}
    {{-- MODAL LIHAT CATATAN --}}
    {{-- ================================================ --}}
    <div id="modalCatatan" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Catatan Petugas</h3>
                <button onclick="closeModal('catatan')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-700" id="catatan_text"></p>
            </div>
        </div>
    </div>


    <script>
        let hargaBeli = 0; // Simpan harga beli untuk dihitung nanti

        function openModal(type) {
            if (type === 'tambah') {
                document.getElementById('modalTambah').classList.remove('hidden');
            } else if (type === 'approve') {
                document.getElementById('modalApprove').classList.remove('hidden');
            }
        }

        function closeModal(type) {
            if (type === 'tambah') {
                document.getElementById('modalTambah').classList.add('hidden');
            } else if (type === 'approve') {
                document.getElementById('modalApprove').classList.add('hidden');
            } else if (type === 'catatan') {
                document.getElementById('modalCatatan').classList.add('hidden');
            }
        }

        function openApproveModal(id, namaAlat, kodeAlat, keterlambatan, hargaBeliAlat) {
            document.getElementById('formApprove').action = '/pengembalian/' + id + '/approve';
            document.getElementById('approve_alat_name').textContent = namaAlat;
            document.getElementById('approve_alat_code').textContent = kodeAlat;
            document.getElementById('approve_keterlambatan').textContent = keterlambatan + ' hari';
            
            // Simpan harga beli untuk kalkulasi nanti
            hargaBeli = hargaBeliAlat || 0;
            
            // Suggest denda berdasarkan keterlambatan (Rp 5.000/hari)
            const suggestedDenda = keterlambatan * 5000;
            document.getElementById('dendaKeterlambatan').value = suggestedDenda;
            document.getElementById('persenKerusakan').value = 0;
            document.getElementById('dendaKerusakan').value = 0;
            document.getElementById('kondisiAlat').value = 'baik';
            document.getElementById('rumusDenda').textContent = '';
            
            // Reset display
            updateTotalDenda();
            handleKondisiChange();
            
            openModal('approve');
        }

        function handleKondisiChange() {
            const kondisi = document.getElementById('kondisiAlat').value;
            const persenKerusakanDiv = document.getElementById('persenKerusakanDiv');
            const dendaKerusakanDiv = document.getElementById('dendaKerusakanDiv');
            
            if (kondisi === 'rusak') {
                persenKerusakanDiv.classList.remove('hidden');
                dendaKerusakanDiv.classList.remove('hidden');
            } else {
                persenKerusakanDiv.classList.add('hidden');
                dendaKerusakanDiv.classList.add('hidden');
                document.getElementById('persenKerusakan').value = 0;
                document.getElementById('dendaKerusakan').value = 0;
                document.getElementById('rumusDenda').textContent = '';
            }
            
            updateTotalDenda();
        }

        function updateDendaKerusakan() {
            const persenKerusakan = parseInt(document.getElementById('persenKerusakan').value) || 0;
            
            // Validasi 0-100
            if (persenKerusakan < 0 || persenKerusakan > 100) {
                alert('Persentase kerusakan harus antara 0-100%');
                document.getElementById('persenKerusakan').value = 0;
                updateTotalDenda();
                return;
            }
            
            // Hitung denda kerusakan (harga_beli * persen / 100)
            const dendaKerusakan = Math.round((hargaBeli * persenKerusakan) / 100);
            
            document.getElementById('dendaKerusakan').value = dendaKerusakan;
            
            // Display info rumus
            const rumusInfo = `Rumus: Rp ${hargaBeli.toLocaleString('id-ID')} × ${persenKerusakan}% = Rp ${dendaKerusakan.toLocaleString('id-ID')}`;
            document.getElementById('rumusDenda').textContent = rumusInfo;
            
            // Display info kerusakan jika >= 70%
            if (persenKerusakan >= 70) {
                document.getElementById('persenInfo').textContent = '⚠️ Alat akan di-mark sebagai RUSAK (kerusakan ≥70%)';
                document.getElementById('persenInfo').classList.remove('hidden');
            } else {
                document.getElementById('persenInfo').classList.add('hidden');
            }
            
            updateTotalDenda();
        }

        function updateTotalDenda() {
            const dendaKeterlambatan = parseInt(document.getElementById('dendaKeterlambatan').value) || 0;
            const dendaKerusakan = parseInt(document.getElementById('dendaKerusakan').value) || 0;
            const totalDenda = dendaKeterlambatan + dendaKerusakan;
            
            document.getElementById('totalDendaDisplay').textContent = 
                'Rp ' + totalDenda.toLocaleString('id-ID');
        }

        function showCatatan(catatan) {
            document.getElementById('catatan_text').textContent = catatan;
            document.getElementById('modalCatatan').classList.remove('hidden');
        }

        // Tutup modal klik di luar
        window.onclick = function(event) {
            if (event.target == document.getElementById('modalTambah')) closeModal('tambah');
            if (event.target == document.getElementById('modalApprove')) closeModal('approve');
            if (event.target == document.getElementById('modalCatatan')) closeModal('catatan');
        }

        // Buka modal tambah kalau ada validation error
        @if($errors->any())
            @if(auth()->user()->level == 'peminjam')
                openModal('tambah');
            @endif
        @endif
    </script>

@endsection