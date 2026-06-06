<?php

namespace App\Http\Controllers;

// 1. Sesuaikan import Form Request ke subfolder yang baru
use App\Http\Requests\StoreBarangRequest;
use App\Http\Requests\UpdateBarangRequest; 
use App\Services\BarangService;
use Exception;

class BarangController extends Controller
{
    // Constructor untuk Dependency Injection ke Service Layer
    public function __construct(
        protected BarangService $barangService
    ) {}

    public function index()
    {
        $barangs = Barang::all();
        
        return response()->json([
            'success' => true,
            'data' => $barangs
        ], 200);
    }

    public function store(StoreBarangRequest $request)
    {
        // Mengambil data yang sudah otomatis lolos validasi dan sanitasi
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

    // 2. Ganti 'Request $request' menjadi 'UpdateBarangRequest $request'
    public function update(UpdateBarangRequest $request, Barang $barang)
    {
        // Validasi manual di sini dihapus karena sudah di-handle oleh UpdateBarangRequest
        $validated = $request->validated();

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