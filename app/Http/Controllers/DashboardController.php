<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Statistik Umum
        $stats = [
            'total_users' => User::count(),
            'total_alat' => Alat::count(),
            'total_kategori' => Kategori::count(),
            
            // Peminjaman
            'peminjaman_pending' => Peminjaman::where('status', 'pending')->count(),
            'peminjaman_aktif' => Peminjaman::where('status', 'approved')->count(),
            'peminjaman_total' => Peminjaman::count(),
            
            // Alat
            'alat_tersedia' => Alat::where('status', 'tersedia')->count(),
            'alat_dipinjam' => Alat::where('status', 'dipinjam')->count(),
            'alat_rusak' => Alat::where('status', 'rusak')->count(),
            
            // Pengembalian
            'pengembalian_pending' => Pengembalian::where('status_pengembalian', 'pending')->count(),
            'pengembalian_total' => Pengembalian::where('status_pengembalian', 'approved')->count(),
            
            // Denda
            'total_denda' => Pengembalian::where('status_pengembalian', 'approved')->sum('total_denda'),
            
            // Keterlambatan
            'peminjaman_terlambat' => Peminjaman::where('status', 'approved')
                ->whereDate('tanggal_kembali_rencana', '<', now())
                ->count(),
        ];

        // Recent Activities (5 terakhir)
        if ($user->level == 'peminjam') {
            // Peminjam: cuma lihat aktivitas dia
            $recentPeminjaman = Peminjaman::with(['alat'])
                ->where('user_id', $user->user_id)
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Admin/Petugas: lihat semua
            $recentPeminjaman = Peminjaman::with(['user', 'alat'])
                ->latest()
                ->take(5)
                ->get();
        }

        // Chart data (untuk admin/petugas)
        $chartData = null;
        if ($user->level != 'peminjam') {
            // Data peminjaman per bulan (6 bulan terakhir)
            $chartData = Peminjaman::select(
                    DB::raw('DATE_TRUNC(\'month\', tanggal_peminjaman) as bulan'),
                    DB::raw('COUNT(*) as total')
                )
                ->where('tanggal_peminjaman', '>=', now()->subMonths(6))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();
        }

        return view('pages.dashboard', compact('stats', 'recentPeminjaman', 'chartData'));
    }
}