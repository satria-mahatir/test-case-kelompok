<?php

use App\Http\Controllers\Api\BukuController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PenerbitController;
use App\Http\Controllers\Api\PenulisController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {

    // Autentikasi (Username & Password - Tanpa Gmail)
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Master Data
    Route::apiResource('kategoris', KategoriController::class);
    Route::apiResource('penulis', PenulisController::class)
        ->parameters(['penulis' => 'penulis']);
    Route::apiResource('penerbits', PenerbitController::class);
    Route::apiResource('bukus', BukuController::class);

    // Manajemen Akun / User
    Route::apiResource('users', UserController::class);

    // Transaksi Peminjaman & Pengembalian
    Route::apiResource('peminjaman', PeminjamanController::class)->except(['update']);
    Route::patch('peminjaman/{peminjaman}/kembalikan', [PeminjamanController::class, 'kembalikan']);
});
