<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\BarangController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', App\Http\Controllers\Api\RegisterController::class);
Route::post('/register1', App\Http\Controllers\Api\RegisterController::class);
Route::post('/login', App\Http\Controllers\Api\LoginController::class);
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/logout', App\Http\Controllers\Api\LogoutController::class);

Route::get('levels', [LevelController::class, 'index']);
Route::post('levels', [LevelController::class, 'store']);
Route::get('levels/{level}', [LevelController::class, 'show']);
Route::put('levels/{level}', [LevelController::class, 'update']);
Route::delete('levels/{level}', [LevelController::class, 'destroy']);


Route::get('users', [UserController::class, 'index']); // Get all users
Route::post('users', [UserController::class, 'store']); // Create a new user
Route::get('users/{user}', [UserController::class, 'show']); // Get a specific user
Route::put('users/{user}', [UserController::class, 'update']); // Update a user
Route::delete('users/{user}', [UserController::class, 'destroy']); // Delete a user



Route::get('kategori', [KategoriController::class, 'index']); // Get all kategori
Route::post('kategori', [KategoriController::class, 'store']); // Create a new category
Route::get('kategori/{category}', [KategoriController::class, 'show']); // Get a specific category
Route::put('kategori/{category}', [KategoriController::class, 'update']); // Update a category
Route::delete('kategori/{category}', [KategoriController::class, 'destroy']); // Delete a category


Route::get('barang', [BarangController::class, 'index']); // Get all barang
Route::post('barang', [BarangController::class, 'store']); // Create a new barang
Route::get('barang/{barang}', [BarangController::class, 'show']); // Get a specific barang
Route::put('barang/{barang}', [BarangController::class, 'update']); // Update a barang
Route::delete('barang/{barang}', [BarangController::class, 'destroy']); // Delete a barang
Route::post('barang/upload', [BarangController::class, 'uploadImage']); // Upload barang image
Route::get('barang', [BarangController::class, 'index']); // Get all barang