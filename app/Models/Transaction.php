<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'type',
        'amount',
        'date',
        'description',
        'category_id',
        'user_id',
        'budget_id',
        'budget_item_id',
    ];

    protected $appends = [
        'formatted_date'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the category that owns the transaction.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the budget associated with the transaction.
     */
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    /**
     * Get the specific budget item (tag) associated with the transaction.
     */
    public function budgetItem()
    {
        return $this->belongsTo(BudgetItem::class, 'budget_item_id');
    }

    public function getFormattedDateAttribute(): string{
        if (!$this->date) {
            return '';
        }

        if ($this->date->isToday()) {
            return 'Today';
        }

        if ($this->date->isYesterday()) {
            return 'Yesterday';
        }

        return $this->date->format('M d, Y');
    }
}
