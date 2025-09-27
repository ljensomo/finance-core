<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function getMonthlyIncomeExpenses(Request $request)
    {
        $userId = auth()->user()->id;

        $monthlyData = Transaction::selectRaw("MONTH(date) as month, type, SUM(amount) as total")
            ->where('user_id', $userId)
            ->whereYear('date', now()->year)
            ->groupBy('month', 'type')
            ->get()
            ->groupBy('type');

        // Pre-fill all months with 0
        $income = array_fill(1, 12, 0);
        $expense = array_fill(1, 12, 0);

        foreach ($monthlyData as $type => $records) {
            foreach ($records as $record) {
                if ($type == 1) {
                    $income[$record->month] = $record->total;
                } elseif ($type == 2) {
                    $expense[$record->month] = $record->total;
                }
            }
        }

        return response()->json([
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'income' => array_values($income),
            'expense' => array_values($expense)
        ]);
    }

    public function getSpendingBreakdown(){
        $userId = auth()->user()->id;
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $spendingData = Transaction::selectRaw("category_id, SUM(amount) as total")
            ->where('user_id', $userId)
            ->where('type', 2)
            ->whereBetween('date', [$start, $end])
            ->with('category')
            ->groupBy('category_id')
            ->get();

        $labels = $spendingData->pluck('category.name');
        $data = $spendingData->pluck('total');

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }
}
