<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Http\Requests\StoreBarangRequest;
use App\Services\BarangService;
use Illuminate\Http\Request;
use Exception;

class BarangController extends Controller
{
    // Cukup tulis constructor ringkas ini (Hapus protected $barangService di atasnya)
    public function __construct(
        protected BarangService $barangService
    ) {}

    public function index()
    {
        // Menggunakan $this->barangService dengan aman
        $barangs = Barang::all();
        
        return response()->json([
            'success' => true,
            'data' => $barangs
        ], 200);
    }

    public function store(StoreBarangRequest $request)
    {
        $validated = $request->validated();

        try {
            $barang = $this->barangService->createBarang($validated);
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil ditambahkan!',
                'data' => $barang
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Barang $barang)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        try {
            $barangUpdated = $this->barangService->updateBarang($barang, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diubah!',
                'data' => $barangUpdated
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Barang $barang)
    {
        try {
            $barang->delete();
            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus!'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang.'
            ], 500);
        }
    }
}