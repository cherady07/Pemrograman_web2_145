<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\Api\UserController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // <-- TAMBAHKAN BARIS INI

// --- ROUTE PROTECTED (Wajib membawa Bearer Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/barang', [BarangController::class, 'index']);
    Route::post('/barang', [BarangController::class, 'store']);
    Route::get('/barang/{barang}', [BarangController::class, 'show']);
    Route::put('/barang/{barang}', [BarangController::class, 'update']);
    Route::delete('/barang/{barang}', [BarangController::class, 'destroy'])->middleware('role:admin');
    
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
    });

});