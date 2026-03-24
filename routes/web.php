<?php

use App\Http\Controllers\ContactPlatformController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierOrderController;
use App\Http\Controllers\SupplierOrderStatusController;
use App\Http\Controllers\SupplierProductOfferController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class)
        ->except(['show']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('contact-platforms', ContactPlatformController::class)
        ->except(['show']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('products', ProductController::class)
        ->except(['show']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('suppliers', SupplierController::class)
        ->except(['show']);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('supplier-product-offer', SupplierProductOfferController::class)
        ->except(['show']);

    Route::patch('/supplier-product-offer/{supplier_product_offer}/availability', [SupplierProductOfferController::class, 'toggleAvailability'])
        ->name('supplier-product-offer.availability');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('supplier-order-statuses', SupplierOrderStatusController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('supplier-orders', SupplierOrderController::class);
});

require __DIR__.'/settings.php';
