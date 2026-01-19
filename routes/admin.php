<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CoverController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\FamilyController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShipmentController;
use App\Http\Controllers\Admin\SubcategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function (){
    return view('admin.dashboard');
})->middleware('can:access dashboard')
    ->name('dashboard');

Route::get('/options', [OptionController::class, 'index'])
    ->middleware('can:manage options')
    ->name('options.index');

Route::resource('families', FamilyController::class)->middleware('can:manage families');
Route::resource('categories', CategoryController::class)->middleware('can:manage categories');
Route::resource('subcategories', SubcategoryController::class)->middleware('can:manage subcategories');
Route::resource('products', ProductController::class)->middleware('can:manage products');
Route::get('products/{product}/variants/{variant}', [ProductController::class, 'variants'])
    ->name('products.variants')
    ->scopeBindings();
Route::put('products/{product}/variants/{variant}', [ProductController::class, 'variantsUpdate'])
    ->name('products.variantsUpdate')
    ->scopeBindings();

Route::resource('covers', CoverController::class)->middleware('can:manage covers');

Route::resource('drivers', DriverController::class)->middleware('can:manage drivers');
Route::get('shipments', [ShipmentController::class, 'index'])
    ->name('shipments.index')
    ->middleware('can:manage shipments');
Route::get('orders', [OrderController::class, 'index'])
    ->name('orders.index')
    ->middleware('can:manage orders');