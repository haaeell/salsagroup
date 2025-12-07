<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RiwayatPesananController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('kategori', KategoriController::class);
    Route::resource('barang', BarangController::class);
    Route::resource('barang-masuk', BarangMasukController::class);
    Route::get('/pesanan/struk/{id}', [PesananController::class, 'struk'])->name('pesanan.struk');

    Route::resource('pesanan', PesananController::class);
    Route::resource('users', UserController::class);

    Route::get('/cari-produk', [PesananController::class, 'cari'])->name('produk.cari');
    Route::get('/riwayat-pesanan', [RiwayatPesananController::class, 'index'])->name('riwayat-pesanan.index');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');
    Route::get('/laporan/detail', [LaporanController::class, 'detail'])->name('laporan.detail');
    Route::get('/account/setting', [AccountController::class, 'edit'])->name('account.setting');
    Route::post('/account/setting', [AccountController::class, 'update'])->name('account.update');
});
