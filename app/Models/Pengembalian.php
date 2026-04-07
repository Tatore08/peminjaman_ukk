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
        'denda_keterlambatan',
        'persen_kerusakan',
        'denda_kerusakan',
        'status_pengembalian',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kembali_aktual' => 'date',
        'denda_keterlambatan' => 'decimal:2',
        'persen_kerusakan' => 'integer',
        'denda_kerusakan' => 'decimal:2',
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
     * Helper: Hitung total denda (keterlambatan + kerusakan)
     */
    public function getTotalDenda()
    {
        return ($this->denda_keterlambatan ?? 0) + ($this->denda_kerusakan ?? 0);
    }

    /**
     * Helper: Format total denda ke Rupiah
     */
    public function getTotalDendaFormatted()
    {
        return 'Rp ' . number_format($this->getTotalDenda(), 0, ',', '.');
    }

    /**
     * Helper: Format denda keterlambatan ke Rupiah
     */
    public function getDendaKeterlambatanFormatted()
    {
        return 'Rp ' . number_format($this->denda_keterlambatan ?? 0, 0, ',', '.');
    }

    /**
     * Helper: Format denda kerusakan ke Rupiah
     */
    public function getDendaKerusakanFormatted()
    {
        return 'Rp ' . number_format($this->denda_kerusakan ?? 0, 0, ',', '.');
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