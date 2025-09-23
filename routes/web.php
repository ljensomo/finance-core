<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth:web')->group(function(){
    // Dashboard routes
    Route::get('/dashboard', function () {
        return view('app');
    })->name('dashboard.index');
    Route::get('/api/dashboard/balance', [DashboardController::class, 'showBalance'])->name('dashboard.balance');
    Route::get('/api/dashboard/recent-transactions', [DashboardController::class, 'showRecentTransactions'])->name('dashboard.transactions');


    // Transaction routes
    Route::get('/transactions', function () {
        return view('app');
    })->name('transactions.index');
    Route::get('/api/transactions', [TransactionController::class, 'list'])->name('transactions.list');
    Route::get('/api/transactions/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/api/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/api/transactions/{id}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/api/transactions/{id}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::post('/api/transactions/import', [TransactionController::class, 'import'])->name('transactions.import');

    // Category routes
    Route::get('/categories', function () {
        return view('app');
    })->middleware('auth')->name('categories.index');
    Route::get('/api/categories', [CategoryController::class, 'list'])->name('categories.list');
    Route::get('/api/categories/{id}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/api/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/api/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/api/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

