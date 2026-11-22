<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    public const TYPE_PURCHASE = 1;
    public const TYPE_TRAVEL   = 2;

    public const TYPES = [
        self::TYPE_PURCHASE => 'Purchase',
        self::TYPE_TRAVEL   => 'Travel',
    ];

    public const PRIORITY_HIGH   = 1;
    public const PRIORITY_MEDIUM = 2;
    public const PRIORITY_LOW    = 3;

    public const PRIORITIES = [
        self::PRIORITY_HIGH   => 'High',
        self::PRIORITY_MEDIUM => 'Medium',
        self::PRIORITY_LOW    => 'Low',
    ];

    public const STATUS_SAVING = 1;
    public const STATUS_READY  = 2;
    public const STATUS_DONE   = 3;

    public const STATUSES = [
        self::STATUS_SAVING => 'Saving',
        self::STATUS_READY  => 'Ready',
        self::STATUS_DONE   => 'Done',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'item',
        'category_id',
        'category',
        'priority',
        'estimated_cost',
        'saved_amount',
        'actual_cost',
        'target_date',
        'end_date',
        'completed_at',
        'status',
        'notes',
        'expense_id',
    ];

    protected $appends = [
        'type_label',
        'category_label',
        'priority_label',
        'status_label',
        'formatted_created_at',
        'formatted_target_date',
        'formatted_date_range',
    ];

    protected $casts = [
        'type'           => 'integer',
        'category_id'    => 'integer',
        'priority'       => 'integer',
        'status'         => 'integer',
        'estimated_cost' => 'decimal:2',
        'saved_amount'   => 'decimal:2',
        'actual_cost'    => 'decimal:2',
        'target_date'    => 'date',
        'end_date'       => 'date',
        'completed_at'   => 'date',
    ];

    // Relationships
    public function categoryModel(): BelongsTo
    {
        return $this->belongsTo(WishlistCategory::class, 'category');
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[(int) $this->type] ?? 'Purchase';
    }

    public function getCategoryLabelAttribute(): string
    {
        if ($this->relationLoaded('categoryModel') && $this->categoryModel) {
            return $this->categoryModel->name;
        }

        if ($this->category) {
            return WishlistCategory::whereKey($this->category)->value('name') ?? 'Uncategorized';
        }

        return 'Uncategorized';
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::PRIORITIES[(int) $this->priority] ?? 'Low';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[(int) $this->status] ?? 'Saving';
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        if (!$this->created_at) {
            return '';
        }

        if ($this->created_at->isToday()) {
            return 'Today at ' . $this->created_at->format('g:i A');
        }

        if ($this->created_at->isYesterday()) {
            return 'Yesterday at ' . $this->created_at->format('g:i A');
        }

        return $this->created_at->format('M d, Y • g:i A');
    }

    public function getFormattedTargetDateAttribute(): string
    {
        if (!$this->target_date) {
            return '';
        }

        if ($this->target_date->isToday()) {
            return 'Today';
        }

        if ($this->target_date->isYesterday()) {
            return 'Yesterday';
        }

        return $this->target_date->format('M d, Y');
    }

    public function getFormattedDateRangeAttribute(): string
    {
        if (!$this->target_date) {
            return '—';
        }

        if ((int) $this->type === self::TYPE_TRAVEL && $this->end_date) {
            return $this->target_date->format('M d, Y') . ' – ' . $this->end_date->format('M d, Y');
        }

        return $this->target_date->format('M d, Y');
    }
}