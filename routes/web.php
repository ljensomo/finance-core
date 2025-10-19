<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WishlistController;
use App\Models\Wishlist;
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
    Route::get('/api/dashboard/balance', [DashboardController::class, 'getBalance'])->name('dashboard.balance');
    Route::get('/api/dashboard/recent-transactions', [DashboardController::class, 'getRecentTransactions'])->name('dashboard.transactions');
    Route::get('/api/dashboard/monthly-income', [DashboardController::class, 'getMonthlyIncome'])->name('dashboard.monthlyIncome');
    Route::get('/api/dashboard/monthly-expenses', [DashboardController::class, 'getMonthlyExpenses'])->name('dashboard.monthlyExpenses');
    Route::get('/api/dashboard/spending-categories', [DashboardController::class, 'getSpendingCategories'])->name('dashboard.spendingCategories');

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

    // Sub-category routes
    Route::get('/sub-categories', function () {
        return view('app');
    })->name('sub-categories.index');
    Route::get('/api/sub-categories', [SubCategoryController::class, 'list'])->name('sub-categories.list');
    Route::get('/api/sub-categories/{id}', [SubCategoryController::class, 'show'])->name('sub-categories.show');
    Route::post('/api/sub-categories', [SubCategoryController::class, 'store'])->name('sub-categories.store');
    Route::put('/api/sub-categories/{id}', [SubCategoryController::class, 'update'])->name('sub-categories.update');
    Route::delete('/api/sub-categories/{id}', [SubCategoryController::class, 'destroy'])->name('sub-categories.destroy');

    // Wishlists routes
    Route::get('/wishlists', function () {
        return view('app');
    })->name('wishlists.index');
    Route::get('/api/wishlists', [WishlistController::class, 'list'])->name('wishlists.list');
    Route::post('/api/wishlists', [WishlistController::class, 'store'])->name('wishlists.store');
    Route::get('/api/wishlists/{id}', [WishlistController::class, 'show'])->name('wishlists.show');
    Route::put('/api/wishlists/{id}', [WishlistController::class, 'update'])->name('wishlists.update');
    Route::delete('/api/wishlists/{id}', [WishlistController::class, 'destroy'])->name('wishlists.destroy');

    // Reports routes
    Route::get('/reports', function () {
        return view('app');
    })->middleware('auth')->name('reports.index');
    Route::get('/api/reports/monthly-income-expenses', [ReportController::class, 'getMonthlyIncomeExpenses'])->name('reports.monthlyIncomeExpenses');
    Route::get('/api/reports/spending-breakdown', [ReportController::class, 'getSpendingBreakdown'])->name('reports.spendingBreakdown');
    Route::post('/api/reports/export-transactions', [App\Exports\TransactionExport::class, 'exportCsv'])->name('reports.exportTransactions');

});

