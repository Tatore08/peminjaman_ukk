<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;  // ← TAMBAH INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlatController extends Controller
{
    /**
     * Tampilkan semua alat (grouped by nama)
     */
    public function index()
    {
        // Ambil semua alat dengan relasi kategori
        $alat = Alat::with('kategori')
            ->orderBy('nama_alat')
            ->orderBy('kode_alat')
            ->get();

        // Group by nama alat untuk display
        $alatGrouped = $alat->groupBy('nama_alat')->map(function($group) {
            $first = $group->first();
            return [
                'nama_alat' => $first->nama_alat,
                'kategori' => $first->kategori->nama_kategori,
                'kategori_id' => $first->kategori_id,
                'deskripsi' => $first->deskripsi,
                'lokasi' => $first->lokasi,
                'total_unit' => $group->count(),
                'tersedia' => $group->where('status', 'tersedia')->count(),
                'dipinjam' => $group->where('status', 'dipinjam')->count(),
                'rusak' => $group->where('status', 'rusak')->count(),
                'units' => $group, // semua unit dalam group ini
            ];
        });

        $kategoriList = Kategori::all();

        return view('pages.alat.index', compact('alatGrouped', 'kategoriList'));
    }

    /**
     * Simpan alat baru (bisa multiple units)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'nama_alat'   => 'required|string|max:100',
            'jumlah'      => 'required|integer|min:1|max:100',
            'deskripsi'   => 'nullable|string',
            'kondisi'     => 'required|in:baik,rusak',
            'lokasi'      => 'nullable|string|max:100',
        ], [
            'kategori_id.required' => 'Kategori harus dipilih',
            'nama_alat.required'   => 'Nama alat harus diisi',
            'jumlah.required'      => 'Jumlah harus diisi',
            'jumlah.min'           => 'Jumlah minimal 1',
            'jumlah.max'           => 'Jumlah maksimal 100',
            'kondisi.required'     => 'Kondisi harus dipilih',
        ]);

        DB::beginTransaction();
        try {
            // Buat sebanyak jumlah yang diminta
            $jumlah = $request->jumlah;
            
            for ($i = 0; $i < $jumlah; $i++) {
                Alat::create([
                    'kategori_id' => $request->kategori_id,
                    'nama_alat'   => $request->nama_alat,
                    'deskripsi'   => $request->deskripsi,
                    'kode_alat'   => null, // auto-generate by trigger
                    'kondisi'     => $request->kondisi,
                    'lokasi'      => $request->lokasi,
                    'status'      => $request->kondisi == 'baik' ? 'tersedia' : 'rusak',
                ]);
            }

            // LOG AKTIVITAS
            LogAktivitas::create([
                'user_id' => Auth::id(),
                'modul' => 'Alat',
                'aktivitas' => "Menambahkan {$jumlah} unit {$request->nama_alat}",
            ]);

            DB::commit();

            return redirect()->route('alat.index')
                ->with('success', "Berhasil menambahkan {$jumlah} unit {$request->nama_alat}!");

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal menambahkan alat: ' . $e->getMessage());
        }
    }

    /**
     * Update alat (single unit)
     */
    public function update(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'kategori_id' => 'required|exists:kategori,kategori_id',
            'nama_alat'   => 'required|string|max:100',
            'deskripsi'   => 'nullable|string',
            'kondisi'     => 'required|in:baik,rusak',
            'lokasi'      => 'nullable|string|max:100',
            'status'      => 'required|in:tersedia,dipinjam,rusak',
        ]);

        $alat->update([
            'kategori_id' => $request->kategori_id,
            'nama_alat'   => $request->nama_alat,
            'deskripsi'   => $request->deskripsi,
            'kondisi'     => $request->kondisi,
            'lokasi'      => $request->lokasi,
            'status'      => $request->status,
        ]);

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'modul' => 'Alat',
            'aktivitas' => "Mengupdate alat {$alat->kode_alat} - {$alat->nama_alat}",
        ]);

        return redirect()->route('alat.index')
            ->with('success', 'Alat berhasil diupdate!');
    }

    /**
     * Hapus alat (single unit)
     */
    public function destroy($id)
    {
        $alat = Alat::findOrFail($id);

        // Cek apakah sedang dipinjam
        if ($alat->status == 'dipinjam') {
            return back()->with('error', 'Tidak bisa menghapus alat yang sedang dipinjam!');
        }

        // Simpan info sebelum dihapus
        $kodeAlat = $alat->kode_alat;
        $namaAlat = $alat->nama_alat;

        $alat->delete();

        // LOG AKTIVITAS
        LogAktivitas::create([
            'user_id' => Auth::id(),
            'modul' => 'Alat',
            'aktivitas' => "Menghapus alat {$kodeAlat} - {$namaAlat}",
        ]);

        return redirect()->route('alat.index')
            ->with('success', 'Alat berhasil dihapus!');
    }
}