<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardControllerv2 extends Controller{

    public function getCurrentMonthExpenses(){
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $expenses = Transaction::where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('type', 2)
            ->sum('amount');

        return response()->json(['total_expenses' => $expenses]);
    }

    public function getOverallExpenses(){
        $overallExpenses = Transaction::where('user_id', Auth::id())
            ->where('type', 2)
            ->sum('amount');

        return response()->json(['total_overall_expenses' => $overallExpenses]);
    }

    public function getCurrentMonthIncome(){
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $income = Transaction::where('user_id', Auth::id())
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('type', 1)
            ->sum('amount');

        return response()->json(['total_income' => $income]);
    }
}