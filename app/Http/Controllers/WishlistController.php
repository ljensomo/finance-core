<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishlistRequest;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $wishlists = Wishlist::where('user_id', Auth::id())->get();
        return response()->json($wishlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWishlistRequest $request)
    {
        $data = $request->validated();
        $isDone = (int) $data['status'] === Wishlist::STATUS_DONE;

        $wishlist = Wishlist::create([
            ...$data,
            'user_id'      => Auth::id(),
            'saved_amount' => $isDone ? ($data['actual_cost'] ?? 0) : ($data['saved_amount'] ?? 0),
            'actual_cost'  => $isDone ? $data['actual_cost'] : null,
            'end_date'     => (int) $data['type'] === Wishlist::TYPE_TRAVEL ? ($data['end_date'] ?? null) : null,
            'completed_at' => $isDone ? ($data['completed_at'] ?? now()->toDateString()) : null,
        ]);

        return response()->json([
            'message' => 'Wishlist item added successfully.',
            'data'    => $wishlist,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $wishlist = Wishlist::findOrFail($id);
        return response()->json($wishlist);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $wishlist = Wishlist::findOrFail($id);
        $wishlist->item = $request->input('item');
        $wishlist->category = $request->input('category');
        $wishlist->type = $request->input('type');
        $wishlist->notes = $request->input('notes');
        $wishlist->priority = $request->input('priority');
        $wishlist->status = $request->input('status');
        $wishlist->estimated_cost = $request->input('estimated_cost');
        $wishlist->target_date = $request->input('target_date');
        $wishlist->save();

        return response()->json('Wishlist updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $wishlist = Wishlist::findOrFail($id);
        $wishlist->delete();

        return response()->json('Wishlist item deleted successfully.');
    }
}
