<?php

namespace App\Http\Requests; // Sesuaikan namespace jika masuk folder Barang

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreBarangRequest extends FormRequest
{
    public function authorize(): bool
{
    return true; // Ubah ke true
}

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

    public function rules(): array
    {
        return [
            // Tambahkan unique jika nama barang tidak boleh kembar di database
            'nama_barang' => 'required|string|max:255|unique:barangs,nama_barang', 
            'harga'       => 'required|numeric|min:0',
            'stok'        => 'required|integer|min:0',
        ];
    }

    public function messages(): array 
    {
        return [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.string'   => 'Nama barang harus berupa teks.',
            'nama_barang.max'      => 'Nama barang maksimal berisi 255 karakter.',
            'nama_barang.unique'   => 'Nama barang ini sudah terdaftar di database.',
            'harga.required'       => 'Harga wajib diisi.',
            'harga.numeric'        => 'Harga harus berupa angka.',
            'harga.min'            => 'Harga minimal 0.',
            'stok.required'        => 'Stok wajib diisi.',
            'stok.integer'         => 'Stok harus berupa bilangan bulat.',
            'stok.min'             => 'Stok minimal 0.',
        ];
    }

    /**
     * MENANGKAP ERROR VALIDASI DAN MENGUBAHNYA MENJADI JSON (Sangat penting untuk API)
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'error',
            'message' => 'Validasi tambah data barang gagal.',
            'errors'  => $validator->errors()
        ], 422));
    }
}