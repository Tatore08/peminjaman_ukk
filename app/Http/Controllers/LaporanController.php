<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Halaman utama laporan
     */
    public function index()
    {
        return view('pages.laporan.index');
    }

    /**
     * Cetak laporan peminjaman (single)
     */
    public function cetakPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'alat.kategori', 'penyetuju'])
            ->findOrFail($id);

        return view('pages.laporan.peminjaman', compact('peminjaman'));
    }

    /**
     * Cetak laporan pengembalian (single)
     */
    public function cetakPengembalian($id)
    {
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
            ->findOrFail($id);

        return view('pages.laporan.pengembalian', compact('pengembalian'));
    }

    /**
     * Halaman rekap periode
     */
    public function rekapPeriode(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai ?? now()->subMonth()->format('Y-m-d');
        $tanggalSelesai = $request->tanggal_selesai ?? now()->format('Y-m-d');

        // Data peminjaman dalam periode
        $peminjaman = Peminjaman::with(['user', 'alat', 'penyetuju'])
            ->whereBetween('tanggal_peminjaman', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal_peminjaman', 'desc')
            ->get();

        // Data pengembalian dalam periode
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
            ->whereBetween('tanggal_kembali_aktual', [$tanggalMulai, $tanggalSelesai])
            ->where('status_pengembalian', 'approved')
            ->get();

        // Statistik
        $stats = [
            'total_peminjaman' => $peminjaman->count(),
            'total_approved' => $peminjaman->where('status', 'approved')->count(),
            'total_pending' => $peminjaman->where('status', 'pending')->count(),
            'total_rejected' => $peminjaman->where('status', 'rejected')->count(),
            'total_returned' => $peminjaman->where('status', 'returned')->count(),
            'total_pengembalian' => $pengembalian->count(),
            'total_denda' => $pengembalian->sum('total_denda'),
            'total_terlambat' => $pengembalian->where('keterlambatan_hari', '>', 0)->count(),
        ];

        return view('pages.laporan.rekap', compact('peminjaman', 'pengembalian', 'stats', 'tanggalMulai', 'tanggalSelesai'));
    }

    /**
     * Cetak rekap periode (print version)
     */
    public function cetakRekap(Request $request)
    {
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = $request->tanggal_selesai;

        // Data peminjaman dalam periode
        $peminjaman = Peminjaman::with(['user', 'alat', 'penyetuju'])
            ->whereBetween('tanggal_peminjaman', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal_peminjaman', 'desc')
            ->get();

        // Data pengembalian dalam periode
        $pengembalian = Pengembalian::with(['peminjaman.user', 'peminjaman.alat'])
            ->whereBetween('tanggal_kembali_aktual', [$tanggalMulai, $tanggalSelesai])
            ->where('status_pengembalian', 'approved')
            ->get();

        // Statistik
        $stats = [
            'total_peminjaman' => $peminjaman->count(),
            'total_approved' => $peminjaman->where('status', 'approved')->count(),
            'total_pending' => $peminjaman->where('status', 'pending')->count(),
            'total_rejected' => $peminjaman->where('status', 'rejected')->count(),
            'total_returned' => $peminjaman->where('status', 'returned')->count(),
            'total_pengembalian' => $pengembalian->count(),
            'total_denda' => $pengembalian->sum('total_denda'),
            'total_terlambat' => $pengembalian->where('keterlambatan_hari', '>', 0)->count(),
        ];

        return view('pages.laporan.cetak-rekap', compact('peminjaman', 'pengembalian', 'stats', 'tanggalMulai', 'tanggalSelesai'));
    }
}