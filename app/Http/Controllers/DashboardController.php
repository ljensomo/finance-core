<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showBalance(){
        $totalIncome = Transaction::where('user_id', auth()->user()->id)->where('type', 1)->sum('amount');
        $totalExpense = Transaction::where('user_id', auth()->user()->id)->where('type', 2)->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return response()->json([
            'income' => $totalIncome,
            'expenses' => $totalExpense,
            'balance' => $balance
        ]);
    }

    public function showRecentTransactions(){
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->user()->id)
            ->orderBy('date', 'desc')
            ->limit(5)
            ->get();
        return response()->json($transactions);
    }
}
