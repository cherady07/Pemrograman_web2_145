<?php

namespace App\Http\Requests; // Sesuaikan namespace jika masuk folder Auth/User

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Mengizinkan request ini dijalankan.
     */
    public function authorize(): bool
    {
        return true; // Wajib diubah ke true
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
     * Aturan validasi untuk memperbarui data User
     */
    public function rules(): array
    {
        // Mengambil ID user yang sedang di-update dari parameter route (misal: /api/users/{id})
        $userId = $this->route('id') ?? $this->route('user');

        return [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => [
                'sometimes',
                'required',
                'email',
                'max:255',
                // Memastikan email unik di tabel users, KECUALI untuk ID user yang sedang di-update ini
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'password' => 'sometimes|required|string|min:6',
            'role'     => 'sometimes|required|in:admin,user',
        ];
    }

    /**
     * Pesan error kustom berbahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'name.required'     => 'Nama tidak boleh kosong.',
            'email.required'    => 'Email tidak boleh kosong.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah digunakan oleh user lain.',
            'password.min'      => 'Password baru minimal harus 6 karakter.',
            'role.in'           => 'Role harus berupa admin atau user.',
        ];
    }

    /**
     * Mengembalikan respon berupa JSON jika validasi gagal
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validasi pembaruan user gagal.',
            'errors'  => $validator->errors()
        ], 422));
    }
}