<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'dashboard')->name('dashboard');
Route::view('/bukus', 'bukus.index')->name('bukus.index');
Route::view('/kategoris', 'kategoris.index')->name('kategoris.index');
Route::view('/penulis', 'penulis.index')->name('penulis.index');
Route::view('/penerbits', 'penerbits.index')->name('penerbits.index');
Route::view('/peminjaman', 'peminjaman.index')->name('peminjaman.index');
