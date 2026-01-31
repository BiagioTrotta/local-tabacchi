<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ProductController;

// Rotta Homepage
Route::get('/', [PageController::class, 'index'])->name('homepage');

// Rotta Prodotti
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');

    // CRUD Movimenti
    Route::get('/inventory', [ProductController::class, 'inventoryForm'])->name('inventory.form');
    Route::post('/inventory/move', [ProductController::class, 'inventoryMove'])->name('inventory.move');
});


Route::get('/products/by-barcode/{ean}', [ProductController::class, 'findByBarcode'])
    ->name('products.byBarcode');



Route::get('/test/reset-warehouse', [PageController::class, 'resetWarehouse'])->name('resetWarehouse');

Route::get('/test/reset-resetBarcode', [PageController::class, 'resetBarcode'])->name('resetBarcode');
