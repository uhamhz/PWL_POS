<?php

use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\SupplierController;


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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [App\Http\Controllers\WelcomeController::class, 'index']);

Route::group(['prefix' => 'user'], function () {
    Route::get('/', [UserController::class, 'index']); // menampilkan halaman awal user
    Route::post('/list', [UserController::class, 'list']); // menampilkan data user dalam bentuk json untuk datatables
    Route::get('/create', [UserController::class, 'create']); // menampilkan halaman form tambah user
    Route::post('/', [UserController::class, 'store']); // menyimpan data user baru
    Route::get('/create_ajax', [UserController::class, 'create_ajax']); // menampilkan halaman form tambah user
    Route::post('/ajax', [UserController::class, 'store_ajax']); //menyimpan data user baru
    Route::get('/{id}', [UserController::class, 'show']); // menampilkan detail user
    Route::get('/{id}/edit', [UserController::class, 'edit']); // menampilkan halaman form edit user
    Route::put('/{id}', [UserController::class, 'update']); // menyimpan perubahan data user
    Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']); // Menampilkan halaman form edit user Ajax  
    Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']); // Menyimpan perubahan data user Ajax
    Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']); // Untuk tampilkan form confirm delete user Ajax
    Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // Untuk hapus data user Ajax
    Route::delete('/{id}', [UserController::class, 'destroy']); // menghapus data user
});

Route::group(['prefix' => 'level'], function () {
    Route::get('/', [LevelController::class, 'index']); // menampilkan halaman awal Level
    Route::post('/list', [LevelController::class, 'list']); // menampilkan data Level dalam bentuk json untuk datatables
    Route::get('/create', [LevelController::class, 'create']); // menampilkan halaman form tambah Level
    Route::post('/', [LevelController::class, 'store']); // menyimpan data Level baru
    Route::get('/{id}/edit', [LevelController::class, 'edit']); // menampilkan halaman form edit Level
    Route::put('/{id}', [LevelController::class, 'update']); // menyimpan perubahan data Level
    Route::delete('/{id}', [LevelController::class, 'destroy']); // menghapus data Level
});

Route::group(['prefix' => 'kategori'], function () {
    Route::get('/', [KategoriController::class, 'index']); // menampilkan halaman awal Kategori
    Route::post('/list', [KategoriController::class, 'list']); // menampilkan data Kategori dalam bentuk json untuk datatables
    Route::get('/create', [KategoriController::class, 'create']); // menampilkan halaman form tambah Kategori
    Route::post('/', [KategoriController::class, 'store']); // menyimpan data Kategori baru
    Route::get('/{id}/edit', [KategoriController::class, 'edit']); // menampilkan halaman form edit Kategori
    Route::put('/{id}', [KategoriController::class, 'update']); // menyimpan perubahan data Kategori
    Route::delete('/{id}', [KategoriController::class, 'destroy']); // menghapus data Kategori
});

Route::group(['prefix' => 'barang'], function () {
    Route::get('/', [BarangController::class, 'index']); // menampilkan halaman awal Barang
    Route::match(['get', 'post'], '/list', [BarangController::class, 'list']); // menampilkan data Barang dalam bentuk json untuk datatables
    Route::get('/create', [BarangController::class, 'create']); // menampilkan halaman form tambah Barang
    Route::post('/', [BarangController::class, 'store']); // menyimpan data Barang baru
    Route::get('/{id}/edit', [BarangController::class, 'edit']); // menampilkan halaman form edit Barang
    Route::get('/{id}', [BarangController::class, 'show']); // menampilkan detail user
    Route::put('/{id}', [BarangController::class, 'update']); // menyimpan perubahan data Barang
    Route::delete('/{id}', [BarangController::class, 'destroy']); // menghapus data Barang
});

Route::group(['prefix' => 'supplier'], function () {
    Route::get('/', [SupplierController::class, 'index']); // menampilkan halaman awal Supplier
    Route::post('/list', [SupplierController::class, 'list']); // menampilkan data Supplier dalam bentuk json untuk datatables
    Route::get('/create', [SupplierController::class, 'create']); // menampilkan halaman form tambah Supplier
    Route::post('/', [SupplierController::class, 'store']); // menyimpan data Supplier baru
    Route::get('/{id}/edit', [SupplierController::class, 'edit']); // menampilkan halaman form edit Supplier
    Route::put('/{id}', [SupplierController::class, 'update']); // menyimpan perubahan data Supplier
    Route::delete('/{id}', [SupplierController::class, 'destroy']); // menghapus data Supplier
});
