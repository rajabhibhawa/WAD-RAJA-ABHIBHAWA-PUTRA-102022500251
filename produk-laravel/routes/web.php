<?php

use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProdukController::class, 'index'])->name('produk.index');
Route::get('/index.php', [ProdukController::class, 'index']);

Route::get('/produk', [ProdukController::class, 'form'])->name('produk.form');
Route::get('/produk.php', [ProdukController::class, 'form']);
Route::get('/produk/{id}/edit', [ProdukController::class, 'form'])->name('produk.edit');

Route::post('/produk', [ProdukController::class, 'simpan'])->name('produk.simpan');
Route::post('/produk.php', [ProdukController::class, 'simpan']);
Route::put('/produk/{id}', [ProdukController::class, 'simpan'])->name('produk.update');

Route::post('/hapus', [ProdukController::class, 'hapus'])->name('produk.hapus');
Route::post('/hapus.php', [ProdukController::class, 'hapus']);
Route::delete('/produk/{id?}', [ProdukController::class, 'hapus']);
