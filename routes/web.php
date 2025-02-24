<?php

use Illuminate\Support\Facades\Route;


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

Route::get('/level', [App\Http\Controllers\LevelController::class, 'index']);
Route::get('/kategori', [App\Http\Controllers\KategoriController::class, 'index']);
Route::get('users', [App\Http\Controllers\UserController::class, 'index']);