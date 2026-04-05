<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\User;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Tampilkan semua log (Admin only)
     */
    public function index(Request $request)
    {
        $query = LogAktivitas::with('user')->orderBy('timestamp', 'desc');

        // Filter by modul
        if ($request->has('modul') && $request->modul != '') {
            $query->where('modul', $request->modul);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        // Filter by tanggal
        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('timestamp', $request->tanggal);
        }

        $logs = $query->paginate(20);

        // Data untuk filter
        $users = User::select('user_id', 'username')->get();
        $moduls = LogAktivitas::select('modul')->distinct()->pluck('modul');

        return view('pages.log.index', compact('logs', 'users', 'moduls'));
    }

    /**
     * Hapus semua log (Admin only)
     */
    public function clear()
    {
        // Catat dulu sebelum hapus
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'modul' => 'Log Aktivitas',
            'aktivitas' => 'Menghapus semua log aktivitas',
        ]);

        // Hapus semua kecuali yang baru saja dibuat
        LogAktivitas::where('log_id', '<', LogAktivitas::max('log_id'))->delete();

        return redirect()->route('log.index')
            ->with('success', 'Semua log berhasil dihapus!');
    }
}