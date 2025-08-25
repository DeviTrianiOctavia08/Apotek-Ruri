<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\PenggunaLoginController;
use App\Http\Controllers\Auth\PenggunaRegisterController;
use App\Http\Controllers\Dashboard\DashboardPemilikController;
use App\Http\Controllers\Dashboard\DashboardApotekerController;
use App\Http\Controllers\Dashboard\DashboardDokterController;
use App\Http\Controllers\Apoteker\ObatController;
use App\Http\Controllers\Apoteker\TransaksiController;
use App\Http\Controllers\Dokter\ResepController;
use App\Http\Controllers\Apoteker\ApotekerResepController;
use App\Http\Controllers\Pemilik\PemilikController;
use App\Http\Controllers\Apoteker\PenggunaController;
use App\Http\Controllers\Pemilik\PemilikTransaksiController;
use App\Http\Controllers\Pemilik\PemilikResepController;
use App\Http\Controllers\Pemilik\PemilikObatController;
use App\Http\Controllers\Pemilik\PemilikPenggunaController;

Route::get('/login', [PenggunaLoginController::class, 'showLoginForm'])->name('pengguna.login.form');
Route::post('/login', [PenggunaLoginController::class, 'login'])->name('pengguna.login');
Route::post('/logout', [PenggunaLoginController::class, 'logout'])->name('pengguna.logout');


Route::get('/register', [PenggunaRegisterController::class, 'showRegisterForm'])->name('pengguna.register.form');
Route::post('/register', [PenggunaRegisterController::class, 'register'])->name('pengguna.register');


// ================== APOTEKER ==================
Route::middleware(['auth:pengguna', 'role:apoteker'])->group(function () {
    Route::get('/apoteker/dashboard', [DashboardApotekerController::class, 'index'])->name('apoteker.dashboard');
    Route::resource('/obat', ObatController::class);
    Route::get('/obat-search', [ObatController::class, 'ajaxSearch'])->name('obat.ajax.search');
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/create', [TransaksiController::class, 'create'])->name('transaksi.create');
    Route::post('/transaksi/store', [TransaksiController::class, 'store'])->name('transaksi.store');
    Route::get('/transaksi/search', [TransaksiController::class, 'ajaxSearch'])->name('transaksi.ajax.search');
    Route::get('/permintaan-resep', [ApotekerResepController::class, 'index'])->name('apoteker.resep.index');
    Route::get('/permintaan-resep/{id}', [ApotekerResepController::class, 'show'])->name('apoteker.resep.show');
    Route::post('/resep/{id}/proses', [ApotekerResepController::class, 'proses'])->name('apoteker.resep.proses');
    Route::resource('/pengguna', PenggunaController::class);
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
    Route::post('/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::get('/pengguna/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit');
    Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');
    Route::get('/pengguna-search', [PenggunaController::class, 'ajaxSearch'])->name('pengguna.ajax.search');
    Route::get('/ajax/pengguna/search', [PenggunaController::class, 'ajaxSearch'])->name('pengguna.ajax.search');

});

// ================== DOKTER ==================
Route::middleware(['auth:pengguna', 'role:dokter'])->group(function () {
    Route::get('/dokter/dashboard', [DashboardDokterController::class, 'index'])->name('dokter.dashboard');
    Route::get('/resep', [ResepController::class, 'index'])->name('resep.index');
    Route::get('/resep/create', [ResepController::class, 'create'])->name('resep.create');
    Route::post('/resep/store', [ResepController::class, 'store'])->name('resep.store');
    Route::get('/resep/{id}', [ResepController::class, 'show'])->name('resep.show');
});

// ================== PEMILIK ==================
Route::middleware(['auth:pengguna', 'role:pemilik'])->group(function () {
    Route::get('/pemilik/dashboard', [DashboardPemilikController::class, 'index'])->name('pemilik.dashboard');
    Route::get('/pemilik/transaksi', [PemilikTransaksiController::class, 'index'])->name('pemilik.transaksi.index');
    Route::post('/pemilik/transaksi/filter', [PemilikTransaksiController::class, 'filter'])->name('pemilik.transaksi.filter');
    Route::get('/pemilik/transaksi/print', [PemilikTransaksiController::class, 'print'])->name('pemilik.transaksi.print');
    Route::get('pemilik/transaksi/download', [PemilikTransaksiController::class, 'download'])->name('pemilik.transaksi.download');
    Route::get('/pemilik/resep', [PemilikResepController::class, 'index'])->name('pemilik.resep.index');
    Route::post('/pemilik/resep/filter', [PemilikResepController::class, 'filter'])->name('pemilik.resep.filter');
    Route::get('pemilik/resep/print', [PemilikResepController::class, 'print'])->name('pemilik.resep.print');
    Route::get('pemilik/resep/download', [PemilikResepController::class, 'download'])->name('pemilik.resep.download');
    Route::get('/pemilik/obat', [PemilikObatController::class, 'index'])->name('pemilik.obat.index');
    Route::post('/pemilik/obat/filter', [PemilikObatController::class, 'filter'])->name('pemilik.obat.filter');
    Route::get('pemilik/obat/print', [PemilikObatController::class, 'print'])->name('pemilik.obat.print');
    Route::get('pemilik/obat/download', [PemilikObatController::class, 'download'])->name('pemilik.obat.download');
    Route::get('/pemilik/pengguna', [PemilikPenggunaController::class, 'index'])->name('pemilik.pengguna.index');
    Route::get('/pemilik/pengguna-search', [PemilikPenggunaController::class, 'ajaxSearch'])->name('pemilik.pengguna.ajax.search');
    Route::get('/pemilik/ajax/pengguna/search', [PemilikPenggunaController::class, 'ajaxSearch'])->name('pemilik.pengguna.ajax.search');
    Route::get('/pemilik/pengguna/print', [PemilikPenggunaController::class, 'print'])->name('pemilik.pengguna.print');
    Route::get('pemilik/pengguna/download', [PemilikPenggunaController::class, 'download'])->name('pemilik.pengguna.download');


});

Route::get('/', function(){
    return redirect('/login');
});