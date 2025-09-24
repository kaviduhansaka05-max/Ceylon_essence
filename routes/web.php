<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\CartController;
use App\Livewire\Products as LivewireProducts;
use App\Http\Controllers\Admin\DashboardController;

Route::middleware(['web','auth'])->group(function () {
    Route::post('/cart/promo/apply',  [CartController::class, 'webApplyPromo'])->name('cart.promo.apply');
    Route::post('/cart/promo/remove', [CartController::class, 'webRemovePromo'])->name('cart.promo.remove');

    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/add', [CartController::class, 'webAdd'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'webUpdateQty'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'webRemove'])->name('cart.remove');
    Route::post('/cart/clear',  [CartController::class, 'webClear'])->name('cart.clear');

    Route::get('/checkout',  [\App\Http\Controllers\PaymentController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [\App\Http\Controllers\PaymentController::class, 'process'])->name('checkout.process');

    Route::get('/order/thanks/{id}', fn ($id) => view('order_thanks', ['orderId' => $id]))->name('order.thanks');
});

Route::get('/products', LivewireProducts::class)->name('products');
Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');

if (app()->environment('local')) {
    Route::get('/mongo-raw', [ProductController::class, 'raw'])->name('mongo.raw');
}

Route::get('/', fn () => view('welcome'))->name('home');

Route::get('/choose-login', fn () => view('auth.chooseuser'))->name('choose.login');

// User auth
Route::get('/login/user',  [AuthenticatedSessionController::class, 'create'])->name('login.user');
Route::post('/login/user', [AuthenticatedSessionController::class, 'store'])->name('login.user.submit');

// Admin auth
Route::get('/login/admin',  [AdminController::class, 'showLoginForm'])->name('login.admin');
Route::post('/login/admin', [AdminController::class, 'login'])->name('login.admin.submit');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

Route::middleware('auth:admin')
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/',           [DashboardController::class, 'index']);
        Route::post('/logout',    [AdminController::class, 'logout'])->name('logout');

        // Customers
        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('users',     [CustomerController::class, 'index'])->name('users.index');

        // Orders
        Route::get('orders',      [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{id}/confirm',  [OrderController::class, 'confirm'])->name('orders.confirm');
        Route::post('orders/{id}/complete', [OrderController::class, 'complete'])->name('orders.complete');
        Route::post('orders/{id}/cancel',   [OrderController::class, 'cancel'])->name('orders.cancel');

        // Products
        Route::get('products',           [AdminProductController::class, 'index'])->name('products.index');
        Route::get('products/create',    [AdminProductController::class, 'create'])->name('products.create');
        Route::get('products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
        Route::post('products',          [AdminProductController::class, 'store'])->name('products.store');
        Route::put('products/{id}',      [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('products/bulk-destroy', [AdminProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');

        // No promos POST here — it lives in routes/api.php
    });

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
