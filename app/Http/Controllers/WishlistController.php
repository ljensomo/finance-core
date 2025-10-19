<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $wishlists = Wishlist::where('user_id', auth()->user()->id)->get();
        return response()->json($wishlists);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $wishlist = new Wishlist();
        $wishlist->user_id = auth()->user()->id;
        $wishlist->item = $request->input('item');
        $wishlist->notes = $request->input('notes');
        $wishlist->priority = $request->input('priority');
        $wishlist->status = $request->input('status');
        $wishlist->estimated_cost = $request->input('estimated_cost');
        $wishlist->save();

        return response()->json($wishlist);
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
        $wishlist->notes = $request->input('notes');
        $wishlist->priority = $request->input('priority');
        $wishlist->status = $request->input('status');
        $wishlist->estimated_cost = $request->input('estimated_cost');
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
