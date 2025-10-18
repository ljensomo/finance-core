<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategory;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $subcategories = SubCategory::with('category')->where('user_id', auth()->user()->id)->get();
        return response()->json($subcategories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $subcategory = new SubCategory();
        $subcategory->user_id = auth()->user()->id;
        $subcategory->category_id = $request->input('category_id');
        $subcategory->name = $request->input('name');
        $subcategory->description = $request->input('description');
        $subcategory->save();

        return response()->json($subcategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subcategory = SubCategory::findOrFail($id);
        return response()->json($subcategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->category_id = $request->input('category_id');
        $subcategory->name = $request->input('name');
        $subcategory->description = $request->input('description');
        $subcategory->save();

        return response()->json('Sub-Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = SubCategory::findOrFail($id);
        $category->delete();

        return response()->json('Sub-Category deleted successfully.');
    }
}
