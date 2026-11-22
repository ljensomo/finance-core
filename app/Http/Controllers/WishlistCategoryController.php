<?php

namespace App\Http\Controllers;

use App\Models\WishlistCategory;
use Illuminate\Http\Request;

class WishlistCategoryController extends Controller
{
    public function list(int $type){
        $categories = WishlistCategory::where('type', $type)->get();

        return response()->json($categories);
    }
}
