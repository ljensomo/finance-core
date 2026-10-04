<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        if($request->has('type')) {
            $categories = Category::where('user_id', auth()->user()->id)
                ->where('type', $request->input('type'))
                ->orderBy('type', 'asc')
                ->get();
        }else{
            $categories = Category::where('user_id', auth()->user()->id)
                ->orderBy('type', 'asc')
                ->get();
        }
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'type'        => ['required', 'integer', Rule::in([1, 2])], // Income, 2: Expense
            'name'        => ['required', 'string', 'max:100'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'], // Hex color code validation
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Create and populate the Category instance
        $category = new Category();
        $category->user_id     = auth()->id();
        $category->type        = $validated['type'];
        $category->name        = $validated['name'];
        $category->icon        = $validated['icon'] ?? null;
        $category->color       = $validated['color'] ?? null;
        $category->description = $validated['description'] ?? null;
        $category->save();

        // 3. Return response with 201 Created status code
        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){
        // Find the category or throw 404
        $category = Category::findOrFail($id);

        // Ensure the category belongs to the authenticated user
        if ($category->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // Validate incoming request data
        $validated = $request->validate([
            'type'        => ['required', 'integer', Rule::in([1, 2])], // 1: Income, 2: Expense
            'name'        => ['required', 'string', 'max:100'],
            'icon'        => ['nullable', 'string', 'max:100'],
            'color'       => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Update model attributes
        $category->type        = $validated['type'];
        $category->name        = $validated['name'];
        $category->icon        = $validated['icon'] ?? null;
        $category->color       = $validated['color'] ?? null;
        $category->description = $validated['description'] ?? null;
        $category->save();

        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json('Category deleted successfully.');
    }
}
