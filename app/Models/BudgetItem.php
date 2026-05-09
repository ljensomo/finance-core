<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_id',
        'item_name',
        'category_id',
        'sub_category_id',
        'amount',
        'description',
        'tag',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'budget_item_id');
    }

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function subCategory(){
        return $this->belongsTo(SubCategory::class);
    }
}
