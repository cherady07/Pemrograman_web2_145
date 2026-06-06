<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
// Import kedua Form Request yang sudah kamu buat sebelumnya
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Exception;

class UserController extends Controller
{
    /**
     * Menampilkan semua daftar user (Read)
     */
    public function index()
    {
        $users = User::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar semua user berhasil diambil.',
            'data'    => $users
        ], 200);
    }

    /**
     * Menambahkan user baru lewat sistem (Create)
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        try {
            // Wajib enkripsi password sebelum dimasukkan ke database
            $validated['password'] = Hash::make($validated['password']);

            $user = User::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'User baru berhasil ditambahkan!',
                'data'    => $user
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menampilkan detail satu user berdasarkan ID
     */
    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail data user berhasil ditemukan.',
            'data'    => $user
        ], 200);
    }

    /**
     * Memperbarui data user (Update)
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        try {
            // Jika dalam request data ada password baru, lakukan hash ulang
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }

            $user->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diperbarui!',
                'data'    => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menghapus akun user (Delete)
     */
    public function destroy(User $user)
    {
        try {
            // Opsional: Mencegah admin menghapus akunnya sendiri secara tidak sengaja
            if (auth()->id() === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak bisa menghapus akun Anda sendiri yang sedang aktif.'
                ], 400);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus dari sistem.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user.'
            ], 500);
        }
    }
}