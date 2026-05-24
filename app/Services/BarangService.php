<?php

namespace App\Services;

use App\Models\Barang;
use Exception;

class BarangService
{
    public function createBarang(array $data)
    {
        try {
            return Barang::create($data);
        } catch (Exception $e) {
            throw new Exception("Gagal menyimpan data barang: " . $e->getMessage());
        }
    }

    public function updateBarang(Barang $barang, array $data)
    {
        try {
            $barang->update($data);
            return $barang;
        } catch (Exception $e) {
            throw new Exception("Gagal mengubah data barang: " . $e->getMessage());
        }
    }
}