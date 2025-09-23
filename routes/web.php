<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController; // public catalog (Mongo)
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\CartController;

Route::middleware(['web','auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');

    // web proxies for Blade forms → call same logic, then redirect
    Route::post('/cart/add',    [CartController::class, 'webAdd'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'webUpdateQty'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'webRemove'])->name('cart.remove');
    Route::post('/cart/clear',  [CartController::class, 'webClear'])->name('cart.clear');
    Route::post('/checkout',    [CartController::class, 'webCheckout'])->name('cart.checkout');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Page (Blade) routes and the specific form targets your Blades submit to.
| API JSON endpoints live in routes/api.php.
*/

/* ---------------- Public catalog (Mongo-backed) ---------------- */
Route::get('/products',      [ProductController::class, 'index'])->name('products');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

if (app()->environment('local')) {
    Route::get('/mongo-raw', [ProductController::class, 'raw'])->name('mongo.raw');
}

/* ---------------- Welcome ---------------- */
Route::get('/', fn () => view('welcome'))->name('home');

/* ---------------- Auth (users + admins) ---------------- */
Route::get('/choose-login', fn () => view('auth.chooseuser'))->name('choose.login');

// User auth (Fortify/Jetstream style)
Route::get('/login/user',  [AuthenticatedSessionController::class, 'create'])->name('login.user');
Route::post('/login/user', [AuthenticatedSessionController::class, 'store'])->name('login.user.submit');

// Admin auth
Route::get('/login/admin',  [AdminController::class, 'showLoginForm'])->name('login.admin');
Route::post('/login/admin', [AdminController::class, 'login'])->name('login.admin.submit');

/* ---------------- Jetstream user dashboard ---------------- */
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

/* ---------------- Admin panel ---------------- */
Route::middleware('auth:admin')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {

        // Dashboard & logout
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout',   [AdminController::class, 'logout'])->name('logout');

        // Users (as before)
        Route::resource('users', UserController::class);

        // Customers page (this is what your sidebar should hit)
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');

        // Products — page routes
        Route::get('products',           [AdminProductController::class, 'index'])->name('products.index');
        Route::get('products/create',    [AdminProductController::class, 'create'])->name('products.create');
        Route::get('products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');

        // Products — form targets used by your Blades
        Route::post('products',                [AdminProductController::class, 'store'])->name('products.store');
        Route::put('products/{id}',            [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('products/bulk-destroy', [AdminProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        // NOTE: Orders resource removed to avoid missing controller error.
        // Add it back only if you actually have:
        // App\Http\Controllers\Admin\OrderController
        // Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class);
    });

/* ---------------- Dev/demo helpers ---------------- */
Route::get('/test-relationships', function () {
    $customer = \App\Models\Customer::with('orders')->first();
    dump($customer ? $customer->toArray() : 'No customer found');

    $order = \App\Models\Order::with(['customer', 'orderItems.product'])->first();
    dump($order ? $order->toArray() : 'No order found');

    $product = \App\Models\Product::with('orderItems')->first();
    dump($product ? $product->toArray() : 'No product found');

    $cart = \App\Models\ShoppingCart::with('cartItems.product')->first();
    dump($cart ? $cart->toArray() : 'No cart found');

    return 'Relationship test completed!';
});
