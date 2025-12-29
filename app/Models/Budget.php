<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    protected $appends = ['remaining'];

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
}
