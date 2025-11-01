<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\GoogleSheetsController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware('auth:web')->group(function(){

    $modules = [
        'transactions' => [
            'controller' => TransactionController::class
        ],
        'categories' => [
            'controller' => CategoryController::class
        ],
        'sub-categories' => [
            'controller' => SubCategoryController::class
        ],
        'wishlists' => [
            'controller' => WishlistController::class
        ],
    ];
    foreach ($modules as $module => $data){
        Route::get('/'.$module, function () {
            return view('app');
        })->name($module.'.index');
        Route::get('/api/'.$module, [$data['controller'], 'list'])->name($module.'.list');
        Route::get('/api/'.$module.'/{id}', [$data['controller'], 'show'])->name($module.'.show');
        Route::post('/api/'.$module, [$data['controller'], 'store'])->name($module.'.store');
        Route::put('/api/'.$module.'/{id}', [$data['controller'], 'update'])->name($module.'.update');
        Route::delete('/api/'.$module.'/{id}', [$data['controller'], 'destroy'])->name($module.'.destroy');
    }

    // Dashboard routes
    Route::get('/dashboard', function () {
        return view('app');
    })->name('dashboard.index');
    Route::get('/api/dashboard/balance', [DashboardController::class, 'getBalance'])->name('dashboard.balance');
    Route::get('/api/dashboard/recent-transactions', [DashboardController::class, 'getRecentTransactions'])->name('dashboard.transactions');
    Route::get('/api/dashboard/monthly-income', [DashboardController::class, 'getMonthlyIncome'])->name('dashboard.monthlyIncome');
    Route::get('/api/dashboard/monthly-expenses', [DashboardController::class, 'getMonthlyExpenses'])->name('dashboard.monthlyExpenses');
    Route::get('/api/dashboard/spending-categories', [DashboardController::class, 'getSpendingCategories'])->name('dashboard.spendingCategories');

    // Monthly Dashboard routes
    Route::get('/monthly-dashboard', function () {
        return view('app');
    })->name('monthlyDashboard.index');
    Route::get('/api/monthly-dashboard/data', [DashboardController::class, 'getMonthlyDashboardData'])->name('monthlyDashboard.data');
    Route::get('/api/monthly-dashboard/summary', [DashboardController::class, 'getMonthFinancialSummary'])->name('monthlyDashboard.summary');

    // Transaction routes
    Route::post('/api/transactions/import', [TransactionController::class, 'import'])->name('transactions.import');

    // Reports routes
    Route::get('/reports', function () {
        return view('app');
    })->middleware('auth')->name('reports.index');
    Route::get('/api/reports/monthly-income-expenses', [ReportController::class, 'getMonthlyIncomeExpenses'])->name('reports.monthlyIncomeExpenses');
    Route::get('/api/reports/spending-breakdown', [ReportController::class, 'getSpendingBreakdown'])->name('reports.spendingBreakdown');
    Route::post('/api/reports/export-transactions', [App\Exports\TransactionExport::class, 'exportCsv'])->name('reports.exportTransactions');

    // Google Routes
    Route::get('/google-sheet/sync', [GoogleSheetsController::class, 'syncTransactions']);


});

