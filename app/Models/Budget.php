<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $appends = [
        'remaining',
        'formatted_start_date',
        'formatted_end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $fillable = [
        'budget_name',
        'start_date',
        'end_date',
        'user_id'
    ];

    public function budgetItems(){
        return $this->hasMany(BudgetItem::class);
    }

    public function transactions(){
        return $this->hasMany(Transaction::class, 'user_id', 'user_id');
    }

    public function getRemainingAttribute(){
        $budgeted = $this->budget ?? 0;
        $actual = $this->actual ?? 0;
        return $budgeted - $actual;
    }

    public function getFormattedStartDateAttribute(): string{
        if (!$this->start_date) {
            return '';
        }

        if ($this->start_date->isToday()) {
            return 'Today';
        }

        if ($this->start_date->isYesterday()) {
            return 'Yesterday';
        }

        return $this->start_date->format('M d, Y');
    }

    public function getFormattedEndDateAttribute(): string{
        if (!$this->end_date) {
            return '';
        }

        if ($this->end_date->isToday()) {
            return 'Today';
        }

        if ($this->end_date->isYesterday()) {
            return 'Yesterday';
        }

        return $this->end_date->format('M d, Y');
    }
}
