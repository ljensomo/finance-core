<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BudgetItem;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(string $id)
    {
        $budgetItems = BudgetItem::with('category')->where('budget_id', $id)->get();
        return response()->json($budgetItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $budgetItem = new BudgetItem();
        $budgetItem->budget_id = $request->input('budget_id');
        $budgetItem->item_name = $request->input('item_name');
        $budgetItem->category_id = $request->input('category_id');
        $budgetItem->sub_category_id = $request->input('sub_category_id');
        $budgetItem->amount = $request->input('amount');
        $budgetItem->description = $request->input('description');
        $budgetItem->tag = $request->input('tag');

        $budgetItem->save();

        return response()->json($budgetItem);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $budgetItem = BudgetItem::findOrFail($id);
        return response()->json($budgetItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $budgetItem = BudgetItem::findOrFail($id);
        $budgetItem->item_name = $request->input('item_name');
        $budgetItem->category_id = $request->input('category_id');
        $budgetItem->sub_category_id = $request->input('sub_category_id');
        $budgetItem->amount = $request->input('amount');
        $budgetItem->description = $request->input('description');
        $budgetItem->tag = $request->input('tag');
        $budgetItem->save();
        
        return response()->json('Budget item updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $budgetItem = BudgetItem::findOrFail($id);
        $budgetItem->delete();

        return response()->json('Budget item deleted successfully.');
    }

}
