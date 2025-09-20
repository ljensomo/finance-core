<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// Route::get('/transactions', [App\Http\Controllers\TransactionController::class, 'index'])->name('transactions');

Route::middleware(['auth'])->group(function () {
    Route::get('/{any}', function () {
            return view('app');
        })->where('any', '.*');
});

