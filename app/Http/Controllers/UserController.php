<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Tampilkan semua user
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    /**
     * Simpan user baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:6',
            'level'    => 'required|in:admin,petugas,peminjam',
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique'   => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min'      => 'Password minimal 6 karakter',
            'level.required'    => 'Level harus dipilih',
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Hash password
            'level'    => $request->level,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cek apakah user sedang edit dirinya sendiri
        if ($user->user_id == Auth::id()) {
            return back()->with('error', 'Anda tidak bisa edit akun sendiri!');
        }

        $request->validate([
            'username' => 'required|string|max:50|unique:users,username,' . $id . ',user_id',
            'password' => 'nullable|string|min:6',
            'level'    => 'required|in:admin,petugas,peminjam',
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique'   => 'Username sudah digunakan',
            'password.min'      => 'Password minimal 6 karakter',
            'level.required'    => 'Level harus dipilih',
        ]);

        // Update data
        $user->username = $request->username;
        $user->level    = $request->level;

        // Update password hanya kalau diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    /**
     * Hapus user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cek apakah user sedang hapus dirinya sendiri
        if ($user->user_id == Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Anda tidak bisa hapus akun sendiri!');
        }

        // Cek apakah user punya peminjaman aktif
        // (nanti diaktifkan kalau tabel peminjaman udah ada)
        if ($user->peminjaman()->whereIn('status', ['pending', 'approved'])->count() > 0) {
            return redirect()->route('users.index')
                ->with('error', 'User tidak bisa dihapus karena masih punya peminjaman aktif!');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}