<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;

// Rotta Homepage
Route::get('/', [PageController::class, 'index'])->name('homepage');

// Rotta Prodotti
Route::get('/products', [ProductController::class, 'index'])->name('products.index');