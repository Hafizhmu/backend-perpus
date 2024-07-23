<?php

use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('buku', [BukuController::class, 'index']);
Route::get('peminjam', [PeminjamController::class, 'index']);
Route::get('riwayat', [PeminjamController::class, 'riwayatPeminjaman']);

Route::post('add/buku', [BukuController::class, 'store']);
Route::post('add/peminjaman', [PeminjamController::class, 'store']);

Route::put('update/buku', [BukuController::class, 'update']);
Route::put('update/peminjaman', [PeminjamController::class, 'update']);

Route::delete('delete/buku/{$id}', [BukuController::class, 'destroy']);
Route::delete('delete/peminjaman/{$id}', [PeminjamController::class, 'destroy']);