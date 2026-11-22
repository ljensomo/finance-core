<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WishlistGroup extends Model
{
    protected $fillable = [
        'name',
        'type',
        'target_date',
        'notes',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'wishlist_group_id');
    }
}
