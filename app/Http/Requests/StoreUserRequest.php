<?php

namespace App\Http\Requests; // Sesuaikan namespace jika masuk folder Auth

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    /**
     * Mengizinkan request ini dijalankan.
     */
    public function authorize(): bool
    {
        return true; // Set true agar proses registrasi/pembuatan user bisa diakses
    }

    /**
     * Membersihkan input dari tag HTML berbahaya (Sanitasi data)
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        array_walk_recursive($input, function (&$val) {
            if (is_string($val)) {
                $val = trim(strip_tags($val));
            }
        });
        
        $this->merge($input);
    }

    /**
     * Aturan validasi untuk pembuatan User baru
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email', // Cek unik ke tabel users
            'password' => 'required|string|min:6', // String dan minimal 6 karakter
            'role'     => 'required|in:admin,user', // Memastikan role yang dimasukkan hanya boleh 'admin' atau 'user'
        ];
    }

    /**
     * Pesan error kustom berbahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal harus 6 karakter.',
            'role.required'     => 'Role wajib diisi.',
            'role.in'           => 'Role harus berupa admin atau user.',
        ];
    }

    /**
     * Mengembalikan respon berupa JSON jika validasi gagal (Sangat penting untuk API/Postman)
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validasi pembuatan user gagal.',
            'errors'  => $validator->errors()
        ], 422));
    }
}