<?php

use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::pattern('id', '[0-9]+'); // ID harus berupa angka

// Authentication Routes
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister']);


Route::middleware(['auth'])->group(function () {
    Route::get('/', [WelcomeController::class, 'index']);
    Route::get('/profil/{id}', [UserController::class, 'indexProfil'])->name('profil.index');
    Route::post('/profil/upload-foto/{id}', [UserController::class, 'uploadFoto'])->name('profil.uploadFoto');

    // Routes untuk Level (ADM, MNG)
    Route::middleware(['authorize:ADM,MNG'])->prefix('level')->group(function () {
        Route::get('/', [LevelController::class, 'index']);
        Route::post('/list', [LevelController::class, 'list']);
        Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']);
        Route::get('/create', [LevelController::class, 'create']);
        Route::post('/', [LevelController::class, 'store']);
        Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
        Route::post('/ajax', [LevelController::class, 'store_ajax']);
        Route::get('/{id}/edit', [LevelController::class, 'edit']);
        Route::put('/{id}', [LevelController::class, 'update']);
        Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
        Route::delete('/{id}', [LevelController::class, 'destroy']);
        Route::get('/import', [LevelController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [LevelController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [LevelController::class, 'export_excel']); // export excel
        Route::get('/export_pdf', [LevelController::class, 'export_pdf']); // export pdf
    });

    // Routes untuk User (ADM, MNG)
    Route::middleware(['authorize:ADM,MNG'])->prefix('user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::post('/list', [UserController::class, 'list']);
        Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']);
        Route::get('/create', [UserController::class, 'create']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/create_ajax', [UserController::class, 'create_ajax']);
        Route::post('/ajax', [UserController::class, 'store_ajax']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::get('/{id}/edit', [UserController::class, 'edit']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/import', [UserController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [UserController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [UserController::class, 'export_excel']); // export excel
        Route::get('/export_pdf', [UserController::class, 'export_pdf']); // export pdf
    });

    // Routes untuk Kategori (ADM, MNG)
    Route::middleware(['authorize:ADM,MNG'])->prefix('kategori')->group(function () {
        Route::get('/', [KategoriController::class, 'index']);
        Route::post('/list', [KategoriController::class, 'list']);
        Route::get('/create', [KategoriController::class, 'create']);
        Route::post('/', [KategoriController::class, 'store']);
        Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);
        Route::post('/ajax', [KategoriController::class, 'store_ajax']);
        Route::get('/{id}/edit', [KategoriController::class, 'edit']);
        Route::put('/{id}', [KategoriController::class, 'update']);
        Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);
        Route::delete('/{id}', [KategoriController::class, 'destroy']);
        Route::get('/import', [KategoriController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [KategoriController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [KategoriController::class, 'export_excel']); // export excel
        Route::get('/export_pdf', [KategoriController::class, 'export_pdf']); // export pdf
    });

    // Routes untuk Supplier (ADM, MNG)
    Route::middleware(['authorize:ADM,MNG'])->prefix('supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::post('/list', [SupplierController::class, 'list']);
        Route::get('/create', [SupplierController::class, 'create']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);
        Route::post('/ajax', [SupplierController::class, 'store_ajax']);
        Route::get('/{id}/edit', [SupplierController::class, 'edit']);
        Route::put('/{id}', [SupplierController::class, 'update']);
        Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
        Route::get('/import', [SupplierController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [SupplierController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [SupplierController::class, 'export_excel']); // export excel
        Route::get('/export_pdf', [SupplierController::class, 'export_pdf']); // export pdf
    });

    // Routes untuk Barang (ADM, MNG, STF)
    Route::middleware(['authorize:ADM,MNG,STF'])->prefix('barang')->group(function () {
        Route::get('/', [BarangController::class, 'index']);
        Route::match(['get', 'post'], '/list', [BarangController::class, 'list']);
        Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']);
        Route::get('/create', [BarangController::class, 'create']);
        Route::post('/', [BarangController::class, 'store']);
        Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
        Route::post('/ajax', [BarangController::class, 'store_ajax']);
        Route::get('/{id}/edit', [BarangController::class, 'edit']);
        Route::get('/{id}', [BarangController::class, 'show']);
        Route::put('/{id}', [BarangController::class, 'update']);
        Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
        Route::delete('/{id}', [BarangController::class, 'destroy']);
        Route::get('/import', [BarangController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [BarangController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [BarangController::class, 'export_excel']); // export excel
        Route::get('/export_pdf', [BarangController::class, 'export_pdf']); // export pdf
    });

    // Routes untuk Stok (ADM, MNG, STF)
    Route::middleware(['authorize:ADM,MNG,STF'])->prefix('stok')->group(function () {
        Route::get('/', [StokController::class, 'index']);
        Route::match(['get', 'post'], '/list', [StokController::class, 'list']);
        Route::get('/{id}/show_ajax', [StokController::class, 'show_ajax']);
        Route::get('/create', [StokController::class, 'create']);
        Route::post('/', [StokController::class, 'store']);
        Route::get('/create_ajax', [StokController::class, 'create_ajax']);
        Route::post('/store_ajax', [StokController::class, 'store_ajax']);
        Route::post('/ajax', [StokController::class, 'store_ajax']);
        Route::get('/{id}/edit', [StokController::class, 'edit']);
        Route::get('/{id}', [StokController::class, 'show']);
        Route::put('/{id}', [StokController::class, 'update']);
        Route::get('/{id}/edit_ajax', [StokController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [StokController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [StokController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [StokController::class, 'delete_ajax']);
        Route::delete('/{id}', [StokController::class, 'destroy']);
        Route::get('/import', [StokController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [StokController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [StokController::class, 'export_excel']); // export excel
        Route::get('/export_pdf', [StokController::class, 'export_pdf']); // export pdf
    });

     // Routes untuk Penjualan (ADM, MNG, STF)
     Route::middleware(['authorize:ADM,MNG,STF'])->prefix('penjualan')->group(function () {
        Route::get('/', [PenjualanController::class, 'index']);
        Route::match(['get', 'post'], '/list', [PenjualanController::class, 'list']);
        Route::get('/{id}/show_ajax', [PenjualanController::class, 'show_ajax']);
        Route::get('/create', [PenjualanController::class, 'create']);
        Route::post('/', [PenjualanController::class, 'store']);
        Route::get('/create_ajax', [PenjualanController::class, 'create_ajax']);
        Route::post('/store_ajax', [PenjualanController::class, 'store_ajax']);
        Route::post('/ajax', [PenjualanController::class, 'store_ajax']);
        Route::get('/{id}/edit', [PenjualanController::class, 'edit']);
        Route::get('/{id}', [PenjualanController::class, 'show']);
        Route::put('/{id}', [PenjualanController::class, 'update']);
        Route::get('/{id}/edit_ajax', [PenjualanController::class, 'edit_ajax']);
        Route::put('/{id}/update_ajax', [PenjualanController::class, 'update_ajax']);
        Route::get('/{id}/delete_ajax', [PenjualanController::class, 'confirm_ajax']);
        Route::delete('/{id}/delete_ajax', [PenjualanController::class, 'delete_ajax']);
        Route::delete('/{id}', [PenjualanController::class, 'destroy']);
        Route::get('/import', [PenjualanController::class, 'import']); // ajax form upload excel
        Route::post('/import_ajax', [PenjualanController::class, 'import_ajax']); // ajax import excel
        Route::get('/export_excel', [PenjualanController::class, 'export_excel']); // export excel
        Route::get('/export_pdf', [PenjualanController::class, 'export_pdf']); // export pdf
    });
});
