<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;


Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::get('/reservas', [ReservationController::class, 'index'])
    ->middleware('auth')
    ->name('reservas.index');

Route::post('/reservas', [ReservationController::class, 'store'])
    ->middleware('auth')
    ->name('reservas.store');

Route::patch('/reservas/{reservation}/finish', [ReservationController::class, 'finish'])
    ->middleware('auth')
    ->name('reservas.finish');


Route::get('/reportes/ventas', [ReportController::class, 'sales'])
    ->middleware('auth')
    ->name('reports.sales');

Route::get('/reportes/ventas/pdf', [ReportController::class, 'salesPdf'])
    ->middleware('auth')
    ->name('reports.sales.pdf');

//productos 
Route::get('/productos/registrar', [ProductController::class, 'create'])
    ->middleware('auth')
    ->name('products.create');

Route::post('/productos', [ProductController::class, 'store'])
    ->middleware('auth')
    ->name('products.store');

Route::get('/tienda', [ProductController::class, 'index'])->name('products.index');
Route::post('/productos/agregar/{product}', [ProductController::class, 'addProduct'])->name('products.add');
Route::post('/productos/vender/{product}', [ProductController::class, 'sellProduct'])->name('products.sell');

Route::get('/productos/historial', [ProductController::class, 'history'])->name('products.history');
Route::get('/productos/historial/pdf', [ProductController::class, 'historyPdf'])->name('products.history.pdf');
