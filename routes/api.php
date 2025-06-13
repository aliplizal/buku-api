<?php

use App\Http\Controllers\BukuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/buku', [BukuController::class, 'index'])->name('show');
Route::get('/buku/create', [BukuController::class, 'create'])->name('create');
Route::post('/buku/store', [BukuController::class, 'store'])->name('store');

Route::POST('/buku/edit/{id}', [BukuController::class, 'update'])->name('update');

Route::delete('/buku/delete/{id}', [BukuController::class, 'destroy'])->name('destroy');