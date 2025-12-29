<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $budgets = Budget::where('user_id', Auth::id())
                ->withSum('budgetItems as budget', 'amount')
                ->withSum(['transactions as actual' => function($query) {
                    $query->whereBetween('date', [DB::raw('budgets.start_date'), DB::raw('budgets.end_date')]);
                }], 'amount')
                ->get();

        return response()->json($budgets);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $budget = new Budget();
        $budget->user_id = Auth::id();
        $budget->budget_name = $request->input('budget_name');
        $budget->start_date = $request->input('start_date');
        $budget->end_date = $request->input('end_date');
        $budget->save();

        return response()->json($budget);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $budget = Budget::findOrFail($id);
        return response()->json($budget);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $budget = Budget::findOrFail($id);
        $budget->budget_name = $request->input('budget_name');
        $budget->start_date = $request->input('start_date');
        $budget->end_date = $request->input('end_date');
        $budget->save();

        return response()->json('Budget updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budget = Budget::findOrFail($id);
        $budget->delete();

        return response()->json('Budget deleted successfully.');
    }
}
