<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistCategory extends Model
{
    protected $table = 'wishlist_categories';

    protected $fillable = [
        'name',
        'type',
        'icon',
        'status',
    ];

    protected $casts = [
        'type'   => 'integer',
        'status' => 'integer',
    ];

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class, 'category');
    }
}
