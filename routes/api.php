<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\PromoController;

// Promo (stateless, no CSRF)
Route::post('/promos', [PromoController::class, 'store'])->name('api.promos.store');

// User info via Sanctum
Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());

// Fortify register via API
Route::post('/register', [RegisteredUserController::class, 'store']);

// v1 API group (authenticated)
Route::middleware('auth:sanctum')->prefix('v1')->as('api.')->group(function () {

    // Products API (Admin)
    Route::get('products', [AdminProductController::class, 'apiIndex'])->name('products.index');
    Route::get('products/{id}', [AdminProductController::class, 'apiShow'])->name('products.show');
    Route::post('products', [AdminProductController::class, 'apiStore'])->name('products.store');
    Route::match(['put','patch'], 'products/{id}', [AdminProductController::class, 'apiUpdate'])->name('products.update');
    Route::delete('products/{id}', [AdminProductController::class, 'apiDestroy'])->name('products.destroy');
    Route::delete('products/bulk-destroy', [AdminProductController::class, 'apiBulkDestroy'])->name('products.bulk-destroy');

    // Orders API (Admin actions)
    Route::post('orders/{id}/confirm',  [AdminOrderController::class, 'confirm'])->name('orders.confirm');
    Route::post('orders/{id}/complete', [AdminOrderController::class, 'complete'])->name('orders.complete');
    Route::post('orders/{id}/cancel',   [AdminOrderController::class, 'cancel'])->name('orders.cancel');

    // Cart API
    Route::get('cart',        [CartController::class, 'apiShow']);
    Route::post('cart/add',   [CartController::class, 'apiAdd']);
    Route::post('cart/update',[CartController::class, 'apiUpdateQty']);
    Route::post('cart/remove',[CartController::class, 'apiRemove']);
    Route::post('cart/clear', [CartController::class, 'apiClear']);

    // Checkout API
    Route::post('checkout',   [CartController::class, 'apiCheckout']);
});
