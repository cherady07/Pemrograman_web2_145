<?php

namespace App\Http\Requests; // Sesuaikan jika kamu pakai folder Product

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateBarangRequest extends FormRequest
{
    /**
     * Mengizinkan request ini dijalankan.
     */
    public function authorize(): bool
    {
        return auth()->check(); // Hanya user/admin yang sudah login yang bisa update barang
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
     * Aturan validasi untuk memperbarui data Barang
     */
    public function rules(): array
    {
        // Ambil ID barang dari parameter route (misal: /api/barang/{id})
        $barangId = $this->route('id') ?? $this->route('barang');

        return [
            'nama_barang' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                // Mengecek unik berdasarkan kolom nama_barang di tabel barangs, 
                // tetapi mengabaikan data barang yang sedang di-update ini sendiri.
                Rule::unique('barangs', 'nama_barang')->ignore($barangId),
            ],
            'harga'       => 'sometimes|required|numeric|min:0',
            'stok'        => 'sometimes|required|integer|min:0',
        ];
    }

    /**
     * Pesan error kustom berbahasa Indonesia
     */
    public function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama barang tidak boleh kosong.',
            'nama_barang.unique'   => 'Nama barang ini sudah ada di database.',
            'harga.required'       => 'Harga tidak boleh kosong.',
            'harga.numeric'        => 'Harga harus berupa angka.',
            'harga.min'            => 'Harga tidak boleh bernilai negatif.',
            'stok.required'        => 'Stok tidak boleh kosong.',
            'stok.integer'         => 'Stok harus berupa bilangan bulat.',
            'stok.min'             => 'Stok tidak boleh bernilai negatif.',
        ];
    }

    /**
     * Mengembalikan respon berupa JSON jika validasi gagal
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validasi perubahan data barang gagal.',
            'errors'  => $validator->errors()
        ], 422));
    }
}