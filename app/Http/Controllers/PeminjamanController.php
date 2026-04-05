<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\User;
use App\Models\LogAktivitas;  // ← TAMBAH INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * Tampilkan semua peminjaman
     */
    public function index()
    {
        $user = Auth::user();

        // Peminjam: cuma lihat punya dia
        if ($user->level == 'peminjam') {
            $peminjaman = Peminjaman::with(['alat.kategori', 'penyetuju'])
                ->where('user_id', $user->user_id)
                ->latest()
                ->paginate(10);
        }
        // Admin & Petugas: lihat semua
        else {
            $peminjaman = Peminjaman::with(['user', 'alat.kategori', 'penyetuju'])
                ->latest()
                ->paginate(10);
        }

        // Data alat yang tersedia (untuk form peminjaman)
        $alatList = Alat::with('kategori')
            ->tersedia() // hanya yang status = tersedia & kondisi = baik
            ->orderBy('nama_alat')
            ->orderBy('kode_alat')
            ->get();

        // Group alat by nama untuk dropdown
        $alatGrouped = $alatList->groupBy('nama_alat')->map(function($group) {
            return [
                'nama' => $group->first()->nama_alat,
                'tersedia' => $group->count(),
                'units' => $group,
            ];
        });

        // User list (untuk admin/petugas)
        $userList = User::where('level', 'peminjam')->get();

        // Kalau ada parameter alat_id dari URL, auto-select di dropdown
        $selectedAlatId = request('alat_id');

        return view('pages.peminjaman.index', compact('peminjaman', 'alatList', 'alatGrouped', 'userList','selectedAlatId'));
    }

    /**
     * Store peminjaman baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'alat_id'                 => 'required|exists:alat,alat_id',
            'tanggal_kembali_rencana' => 'required|date|after:today',
            'catatan'                 => 'nullable|string',
            'user_id'                 => 'nullable|exists:users,user_id',
        ], [
            'alat_id.required'                 => 'Alat harus dipilih',
            'tanggal_kembali_rencana.required' => 'Tanggal kembali harus diisi',
            'tanggal_kembali_rencana.after'    => 'Tanggal kembali harus setelah hari ini',
        ]);

        // Cek apakah alat masih tersedia
        $alat = Alat::findOrFail($request->alat_id);
        
        if ($alat->status != 'tersedia') {
            return back()->withInput()
                ->with('error', 'Alat ' . $alat->kode_alat . ' sudah tidak tersedia!');
        }

        // Tentukan siapa peminjam
        $peminjamId = $user->level == 'peminjam' 
            ? $user->user_id 
            : ($request->user_id ?? $user->user_id);

        Peminjaman::create([
            'user_id'                 => $peminjamId,
            'alat_id'                 => $request->alat_id,
            'tanggal_peminjaman'      => now(),
            'tanggal_kembali_rencana' => $request->tanggal_kembali_rencana,
            'status'                  => 'pending',
            'catatan'                 => $request->catatan,
        ]);

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'modul' => 'Peminjaman',
            'aktivitas' => "Mengajukan peminjaman {$alat->nama_alat} ({$alat->kode_alat})",
        ]);

        return redirect()->route('peminjaman.index')
            ->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan.');
    }

    /**
     * Approve peminjaman (Admin & Petugas)
     */
    public function approve($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status != 'pending') {
            return back()->with('error', 'Peminjaman sudah diproses!');
        }

        // Cek lagi apakah alat masih tersedia
        $alat = $peminjaman->alat;
        if ($alat->status != 'tersedia') {
            return back()->with('error', 'Alat sudah tidak tersedia!');
        }

        DB::beginTransaction();
        try {
            // Update status peminjaman
            $peminjaman->update([
                'status' => 'approved',
                'disetujui_oleh' => Auth::user()->user_id,
            ]);

            // Update status alat jadi dipinjam
            $alat->update(['status' => 'dipinjam']);

            // LOG AKTIVITAS
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'modul' => 'Peminjaman',
                'aktivitas' => "Menyetujui peminjaman #{$peminjaman->peminjaman_id} - {$alat->nama_alat} ({$alat->kode_alat}) oleh {$peminjaman->user->username}",
            ]);

            DB::commit();

            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil disetujui!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal approve peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Reject peminjaman (Admin & Petugas)
     */
    public function reject($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status != 'pending') {
            return back()->with('error', 'Peminjaman sudah diproses!');
        }

        $peminjaman->update([
            'status' => 'rejected',
            'disetujui_oleh' => Auth::user()->user_id,
        ]);

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'modul' => 'Peminjaman',
            'aktivitas' => "Menolak peminjaman #{$peminjaman->peminjaman_id} - {$peminjaman->alat->nama_alat} oleh {$peminjaman->user->username}",
        ]);

        return redirect()->route('peminjaman.index')
            ->with('success', 'Peminjaman ditolak!');
    }

    /**
     * Cancel peminjaman (Peminjam, hanya yang pending)
     */
    public function cancel($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $user = Auth::user();

        // Cek ownership
        if ($peminjaman->user_id != $user->user_id) {
            return back()->with('error', 'Anda tidak berhak membatalkan peminjaman ini!');
        }

        if ($peminjaman->status != 'pending') {
            return back()->with('error', 'Hanya peminjaman pending yang bisa dibatalkan!');
        }

        $peminjaman->update(['status' => 'rejected']);

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'modul' => 'Peminjaman',
            'aktivitas' => "Membatalkan peminjaman #{$peminjaman->peminjaman_id} - {$peminjaman->alat->nama_alat}",
        ]);

        return redirect()->route('peminjaman.index')
            ->with('success', 'Peminjaman berhasil dibatalkan!');
    }

    /**
     * Hapus peminjaman (Admin only)
     */
    public function destroy($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        DB::beginTransaction();
        try {
            // Kalau statusnya approved, kembalikan status alat
            if ($peminjaman->status == 'approved') {
                $peminjaman->alat->update(['status' => 'tersedia']);
            }

            // Simpan info sebelum dihapus
            $alatNama = $peminjaman->alat->nama_alat;
            $alatKode = $peminjaman->alat->kode_alat;

            $peminjaman->delete();

            // LOG AKTIVITAS
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'modul' => 'Peminjaman',
                'aktivitas' => "Menghapus peminjaman #{$id} - {$alatNama} ({$alatKode})",
            ]);

            DB::commit();

            return redirect()->route('peminjaman.index')
                ->with('success', 'Peminjaman berhasil dihapus!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal hapus peminjaman: ' . $e->getMessage());
        }
    }
}