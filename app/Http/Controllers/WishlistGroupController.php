<?php

namespace App\Http\Controllers;

use App\Models\WishlistGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistGroupController extends Controller
{
    public function index(): JsonResponse
    {
        $groups = WishlistGroup::orderBy('name')->get();

        return response()->json($groups);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'type' => ['nullable', 'integer', 'in:1,2'],
            'target_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $cleanName = trim($validated['name']);

        // Reuse existing group if a matching name already exists
        $existing = WishlistGroup::whereRaw('LOWER(name) = ?', [mb_strtolower($cleanName)])->first();
        if ($existing) {
            return response()->json($existing, 200);
        }

        $group = WishlistGroup::create([
            'name' => $cleanName,
            'type' => $validated['type'] ?? null,
            'target_date' => $validated['target_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json($group, 201);
    }
}
