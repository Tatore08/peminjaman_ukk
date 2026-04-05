<?php

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\Peminjaman;
use App\Models\LogAktivitas;  // ← TAMBAH INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    /**
     * Tampilkan semua pengembalian
     */
    public function index()
    {
        $user = Auth::user();

        // Admin & Petugas: lihat semua
        if ($user->level == 'admin' || $user->level == 'petugas') {
            $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
                ->latest()
                ->paginate(10);
            
            // Data peminjaman yang sudah approved (bisa dikembalikan)
            $peminjamanList = Peminjaman::with(['user', 'alat'])
                ->where('status', 'approved')
                ->whereDoesntHave('pengembalian')
                ->get();
        } 
        // Peminjam: cuma lihat punya dia
        else {
            $pengembalian = Pengembalian::with(['peminjaman.alat'])
                ->whereHas('peminjaman', function($q) use ($user) {
                    $q->where('user_id', $user->user_id);
                })
                ->latest()
                ->paginate(10);
            
            // Peminjaman milik dia yang sudah approved (bisa dikembalikan)
            $peminjamanList = Peminjaman::with('alat')
                ->where('user_id', $user->user_id)
                ->where('status', 'approved')
                ->whereDoesntHave('pengembalian')
                ->get();
        }

        return view('pages.pengembalian.index', compact('pengembalian', 'peminjamanList'));
    }

    /**
     * Peminjam ajukan pengembalian (status: pending)
     */
    public function store(Request $request)
    {
        $request->validate([
            'peminjaman_id' => 'required|exists:peminjaman,peminjaman_id',
        ], [
            'peminjaman_id.required' => 'Peminjaman harus dipilih',
        ]);

        // Cek apakah peminjaman sudah approved
        $peminjaman = Peminjaman::findOrFail($request->peminjaman_id);
        
        if ($peminjaman->status != 'approved') {
            return back()->with('error', 'Peminjaman belum disetujui!');
        }

        // Cek apakah sudah pernah dikembalikan
        if ($peminjaman->pengembalian) {
            return back()->with('error', 'Peminjaman ini sudah pernah dikembalikan!');
        }

        // Hitung keterlambatan
        $tanggalKembali = now();
        $tanggalRencana = $peminjaman->tanggal_kembali_rencana;
        $keterlambatan = $tanggalKembali->greaterThan($tanggalRencana) 
            ? (int) $tanggalRencana->diffInDays($tanggalKembali) 
            : 0;

        // Buat pengembalian dengan status pending
        Pengembalian::create([
            'peminjaman_id'          => $request->peminjaman_id,
            'tanggal_kembali_aktual' => $tanggalKembali->toDateString(),
            'kondisi_alat'           => 'baik', // default, nanti petugas yang ubah
            'keterlambatan_hari'     => $keterlambatan,
            'total_denda'            => 0, // nanti petugas yang input manual
            'status_pengembalian'    => 'pending',
            'catatan'                => null, // nanti petugas yang isi
        ]);

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'modul' => 'Pengembalian',
            'aktivitas' => "Mengajukan pengembalian {$peminjaman->alat->nama_alat} ({$peminjaman->alat->kode_alat})",
        ]);

        return redirect()->route('pengembalian.index')
            ->with('success', 'Pengembalian berhasil diajukan! Menunggu pengecekan petugas.');
    }

    /**
     * Petugas approve pengembalian (input denda + catatan + kondisi)
     */
    public function approve(Request $request, $id)
    {
        $pengembalian = Pengembalian::findOrFail($id);

        if ($pengembalian->status_pengembalian != 'pending') {
            return back()->with('error', 'Pengembalian sudah diproses!');
        }

        $request->validate([
            'kondisi_alat' => 'required|in:baik,rusak',
            'total_denda'  => 'required|numeric|min:0',
            'catatan'      => 'nullable|string',
        ], [
            'kondisi_alat.required' => 'Kondisi alat harus dipilih',
            'total_denda.required'  => 'Denda harus diisi',
            'total_denda.min'       => 'Denda minimal 0',
        ]);

        DB::beginTransaction();
        try {
            // Update pengembalian dengan data dari petugas
            $pengembalian->update([
                'kondisi_alat'        => $request->kondisi_alat,
                'total_denda'         => $request->total_denda,
                'catatan'             => $request->catatan,
                'status_pengembalian' => 'approved', // Trigger akan update status peminjaman & alat
            ]);

            // LOG AKTIVITAS
            $dendaText = $request->total_denda > 0 
                ? 'dengan denda Rp ' . number_format($request->total_denda, 0, ',', '.')
                : 'tanpa denda';
            
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'modul' => 'Pengembalian',
                'aktivitas' => "Menyetujui pengembalian #{$pengembalian->pengembalian_id} - {$pengembalian->peminjaman->alat->nama_alat} {$dendaText}, kondisi: {$request->kondisi_alat}",
            ]);

            DB::commit();

            return redirect()->route('pengembalian.index')
                ->with('success', 'Pengembalian berhasil disetujui!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal approve pengembalian: ' . $e->getMessage());
        }
    }

    /**
     * Petugas reject pengembalian
     */
    public function reject($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);

        if ($pengembalian->status_pengembalian != 'pending') {
            return back()->with('error', 'Pengembalian sudah diproses!');
        }

        // Simpan info sebelum dihapus
        $alatNama = $pengembalian->peminjaman->alat->nama_alat;
        $alatKode = $pengembalian->peminjaman->alat->kode_alat;

        // Hapus pengembalian yang ditolak
        $pengembalian->delete();

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'modul' => 'Pengembalian',
            'aktivitas' => "Menolak pengembalian #{$id} - {$alatNama} ({$alatKode})",
        ]);

        return redirect()->route('pengembalian.index')
            ->with('success', 'Pengembalian ditolak! Peminjam harus ajukan ulang.');
    }

    /**
     * Hapus pengembalian (Admin only)
     */
    public function destroy($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);
        $peminjaman = $pengembalian->peminjaman;

        DB::beginTransaction();
        try {
            // Kalau pengembalian udah approved, kembalikan status
            if ($pengembalian->status_pengembalian == 'approved') {
                // Kembalikan status peminjaman ke 'approved'
                $peminjaman->update(['status' => 'approved']);

                // Kembalikan status alat ke 'dipinjam'
                $peminjaman->alat->update(['status' => 'dipinjam']);
            }

            // Simpan info sebelum dihapus
            $alatNama = $peminjaman->alat->nama_alat;
            $alatKode = $peminjaman->alat->kode_alat;

            // Hapus pengembalian
            $pengembalian->delete();

            // LOG AKTIVITAS
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'modul' => 'Pengembalian',
                'aktivitas' => "Menghapus pengembalian #{$id} - {$alatNama} ({$alatKode})",
            ]);

            DB::commit();

            return redirect()->route('pengembalian.index')
                ->with('success', 'Pengembalian berhasil dibatalkan!');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal batalkan pengembalian: ' . $e->getMessage());
        }
    }
}