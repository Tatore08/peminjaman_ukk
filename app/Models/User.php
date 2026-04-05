<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Nama tabel
     */
    protected $table = 'users';

    /**
     * Primary key
     */
    protected $primaryKey = 'user_id';

    /**
     * Auto increment
     */
    public $incrementing = true;

    /**
     * Tipe primary key
     */
    protected $keyType = 'int';

    /**
     * Timestamp aktif
     */
    public $timestamps = true;

    /**
     * Kolom yang boleh diisi mass assignment
     */
    protected $fillable = [
        'username',
        'password',
        'level',
    ];

    /**
     * Kolom yang disembunyikan
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Cast
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Login pakai kolom username
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Primary key untuk Auth
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Relasi: user -> peminjaman
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'user_id', 'user_id');
    }

    /**
     * Relasi: user -> peminjaman yang disetujui
     */
    public function peminjamanDisetujui()
    {
        return $this->hasMany(Peminjaman::class, 'disetujui_oleh', 'user_id');
    }

    /**
     * Relasi: user -> log aktivitas
     */
    public function logAktivitas()
    {
        return $this->hasMany(LogAktivitas::class, 'user_id', 'user_id');
    }

    /**
     * Helper role
     */
    public function isAdmin()
    {
        return $this->level === 'admin';
    }

    public function isPetugas()
    {
        return $this->level === 'petugas';
    }

    public function isPeminjam()
    {
        return $this->level === 'peminjam';
    }

    /**
     * Scope filter level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Auto hash password
     */
    public function setPasswordAttribute($value)
    {
        if (\Illuminate\Support\Facades\Hash::needsRehash($value)) {
            $this->attributes['password'] = bcrypt($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
