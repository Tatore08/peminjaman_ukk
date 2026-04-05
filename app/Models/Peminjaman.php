<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $primaryKey = 'peminjaman_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'alat_id',
        'tanggal_peminjaman',
        'tanggal_kembali_rencana',
        'status',
        'disetujui_oleh',
        'catatan',
    ];

    protected $casts = [
        'tanggal_peminjaman' => 'date',
        'tanggal_kembali_rencana' => 'date',
    ];

    /**
     * Relasi ke User (peminjam)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Relasi ke Alat
     */
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id', 'alat_id');
    }

    /**
     * Relasi ke User (yang menyetujui)
     */
    public function penyetuju()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh', 'user_id');
    }

    /**
     * Relasi ke Pengembalian
     */
    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id', 'peminjaman_id');
    }

    /**
     * Helper: Cek apakah terlambat
     */
    public function isTerlambat()
    {
        if ($this->status != 'approved') {
            return false;
        }

        return now()->greaterThan($this->tanggal_kembali_rencana);
    }

    /**
     * Helper: Hitung hari keterlambatan
     */
    public function hariTerlambat()
    {
        if (!$this->isTerlambat()) {
            return 0;
        }

        return $this->tanggal_kembali_rencana->diffInDays(now());
    }
}