<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;

// Keep this
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());

// Keep Fortify register
Route::post('/register', [RegisteredUserController::class, 'store']);

// IMPORTANT: use api.* names (NOT admin.*) to avoid clashing with Blade form routes.
Route::middleware('auth:sanctum')->prefix('v1')->as('api.')->group(function () {
    Route::get('products', [AdminProductController::class, 'apiIndex'])->name('products.index');
    Route::get('products/{id}', [AdminProductController::class, 'apiShow'])->name('products.show');
    Route::post('products', [AdminProductController::class, 'apiStore'])->name('products.store');
    Route::match(['put','patch'], 'products/{id}', [AdminProductController::class, 'apiUpdate'])->name('products.update');
    Route::delete('products/{id}', [AdminProductController::class, 'apiDestroy'])->name('products.destroy');
    Route::delete('products/bulk-destroy', [AdminProductController::class, 'apiBulkDestroy'])->name('products.bulk-destroy');
});

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::get('cart',        [CartController::class, 'apiShow']);
    Route::post('cart/add',   [CartController::class, 'apiAdd']);
    Route::post('cart/update',[CartController::class, 'apiUpdateQty']);
    Route::post('cart/remove',[CartController::class, 'apiRemove']);
    Route::post('cart/clear', [CartController::class, 'apiClear']);
    Route::post('checkout',   [CartController::class, 'apiCheckout']);
});



