<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';
    protected $primaryKey = 'pengembalian_id';
    public $timestamps = true;

    protected $fillable = [
        'peminjaman_id',
        'tanggal_kembali_aktual',
        'kondisi_alat',
        'keterlambatan_hari',
        'total_denda',
        'status_pengembalian', // pending, approved
        'catatan', // catatan petugas tentang kerusakan
    ];

    protected $casts = [
        'tanggal_kembali_aktual' => 'date',
        'total_denda' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Peminjaman
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id', 'peminjaman_id');
    }

    /**
     * Helper: Format denda ke Rupiah
     */
    public function getDendaFormatted()
    {
        return 'Rp ' . number_format($this->total_denda ?? 0, 0, ',', '.');
    }

    /**
     * Helper: Cek apakah pending
     */
    public function isPending()
    {
        return $this->status_pengembalian === 'pending';
    }

    /**
     * Helper: Cek apakah approved
     */
    public function isApproved()
    {
        return $this->status_pengembalian === 'approved';
    }
}