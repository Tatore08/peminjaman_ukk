<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';
    protected $primaryKey = 'alat_id';
    public $timestamps = true;

    protected $fillable = [
        'kategori_id',
        'nama_alat',
        'deskripsi',
        'kode_alat',
        'kondisi',
        'lokasi',
        'status', // tersedia, dipinjam, rusak
    ];

    /**
     * Relasi ke Kategori
     */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'kategori_id');
    }

    /**
     * Relasi ke Peminjaman
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'alat_id', 'alat_id');
    }

    /**
     * Scope: Alat yang tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')
                     ->where('kondisi', 'baik');
    }

    /**
     * Helper: Cek apakah alat sedang dipinjam
     */
    public function isDipinjam()
    {
        return $this->status === 'dipinjam';
    }

    /**
     * Helper: Get nama lengkap dengan kode
     */
    public function getNamaLengkap()
    {
        return $this->nama_alat . ' (' . $this->kode_alat . ')';
    }
}