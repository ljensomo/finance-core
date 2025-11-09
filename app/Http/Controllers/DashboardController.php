<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{

    private $savings_category_id = 20; // Assuming 20 is the ID for the 'Savings' category
    private $debt_payments_category_id = 18; // Assuming 18 is the ID for the 'Debt Payments' category
    private $currentYear;
    private $currentMonth;

    public function __construct()
    {
        $this->currentYear = now()->year;
        $this->currentMonth = now()->month;
    }

    public function getFinanceOverview(){
        $totalIncome = Transaction::where('user_id', Auth::id())->where('type', 1)->sum('amount');
        $totalExpense = Transaction::where('user_id', Auth::id())->where('type', 2)->sum('amount');
        $totalSavings = Transaction::where('user_id', Auth::id())
                        ->where('category_id', $this->savings_category_id)
                        ->sum('amount');
        $totalDebtPayments = Transaction::where('user_id', Auth::id())
                        ->where('category_id', $this->debt_payments_category_id)
                        ->sum('amount');
        $net = $totalIncome - $totalExpense;

        return response()->json([
            'income' => $totalIncome,
            'expenses' => $totalExpense,
            'savings' => $totalSavings,
            'debt_payments' => $totalDebtPayments,
            'net' => $net
        ]);
    }

    public function getRecentTransactions(){
        $transactions = Transaction::with('category')
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();
        return response()->json($transactions);
    }

    public function getMonthlyIncome(){
        $currentYear = $this->currentYear;
        $currentMonth = $this->currentMonth;

        $monthlyIncome = Transaction::select(DB::raw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total_income'))
            ->where('user_id', Auth::id())
            ->where('type', 1)
            ->where(function($query) use ($currentYear, $currentMonth){
                $query->whereRaw('YEAR(date) != ?', [$currentYear])
                      ->orWhereRaw('MONTH(date) < ?', [$currentMonth]);
            })
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->get()
            ->avg('total_income');
        return response()->json($monthlyIncome);
    }

    public function getMonthlyExpenses(){
        $currentYear = $this->currentYear;
        $currentMonth = $this->currentMonth;

        $monthlyExpenses = Transaction::select(DB::raw('YEAR(date) as year, MONTH(date) as month, SUM(amount) as total_expenses'))
            ->where('user_id', Auth::id())
            ->where('type', 2)
            ->where(function($query) use ($currentYear, $currentMonth){
                $query->whereRaw('YEAR(date) != ?', [$currentYear])
                      ->orWhereRaw('MONTH(date) < ?', [$currentMonth]);
            })
            ->groupBy(DB::raw('YEAR(date), MONTH(date)'))
            ->get()
            ->avg('total_expenses');
        return response()->json($monthlyExpenses);
    }

    public function getSpendingCategories(){
        $spendingCategories = Transaction::select('category_id', DB::raw('SUM(amount) as total_spent'))
            ->where('user_id', Auth::id())
            ->where('type', 2)
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Transform the result into {"Category Name": total_spent}
        $formatted = $spendingCategories->mapWithKeys(function ($item) {
            return [
                $item->category->name => [
                    'value' => $item->total_spent,
                    'color' => $item->category->color ?? '#CCCCCC'
                ]
            ];
        });
        return response()->json($formatted);
    }

    public function getMonthlyDashboardData(Request $request){
        $monthInput = $request->input('monthYear', date('Y-m'));

        $categories = Category::where('user_id', Auth::id())->where('type', 2)->get(['name', 'color', 'id']);
        $totals = Transaction::selectRaw("category_id, SUM(amount) as total")
                    ->where('user_id', Auth::id())
                    ->where('type', 2)
                    ->whereMonth('date', Carbon::parse($monthInput)->format('m'))
                    ->whereYear('date', Carbon::parse($monthInput)->format('Y'))
                    ->groupBy('category_id')
                    ->get()
                    ->pluck('total', 'category_id');

        $labels = [];
        $values = [];
        $colors = [];

        foreach($categories as $category){
            $labels[] = $category->name;
            $values[] = $totals[$category->id] ?? 0;
            $colors[] = $category->color ?? '#CCCCCC';
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
            'colors' => $colors
        ]);
    }

    public function getMonthFinancialSummary(Request $request){
        $monthInput = $request->input('monthYear', date('Y-m'));

        $totalIncome = Transaction::where('user_id', Auth::id())
                        ->where('type', 1)
                        ->whereMonth('date', Carbon::parse($monthInput)->format('m'))
                        ->whereYear('date', Carbon::parse($monthInput)->format('Y'))
                        ->sum('amount');

        $totalExpense = Transaction::where('user_id', Auth::id())
                        ->where('type', 2)
                        ->whereMonth('date', Carbon::parse($monthInput)->format('m'))
                        ->whereYear('date', Carbon::parse($monthInput)->format('Y'))
                        ->sum('amount');

        $totalSavings = Transaction::where('user_id', Auth::id())
                        ->where('category_id', $this->savings_category_id)
                        ->whereMonth('date', Carbon::parse($monthInput)->format('m'))
                        ->whereYear('date', Carbon::parse($monthInput)->format('Y'))
                        ->sum('amount');

        $totalDebtPayments = Transaction::where('user_id', Auth::id())
                        ->where('category_id', $this->debt_payments_category_id) 
                        ->whereMonth('date', Carbon::parse($monthInput)->format('m'))
                        ->whereYear('date', Carbon::parse($monthInput)->format('Y'))
                        ->sum('amount');

        return response()->json([
            'income' => $totalIncome,
            'expense' => $totalExpense,
            'savings' => $totalSavings,
            'debt_payments' => $totalDebtPayments
        ]);
    }

    public function getTotalSavings(){
        $totalSavings = Transaction::where('user_id', Auth::id())
                        ->where('category_id', $this->savings_category_id)
                        ->sum('amount');

        return response()->json([
            'savings' => $totalSavings
        ]);
    }
}
