<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model{

    protected $appends = [
        'category_label',
        'type_label',
        'formatted_created_at',
        'formatted_target_date',
    ];

    // Cast target_date so Eloquent converts it to a Carbon instance automatically
    protected $casts = [
        'target_date' => 'date',
    ];

    public const CATEGORIES = [
        1 => 'Exterior & Building',
        2 => 'Living Room',
        3 => 'Kitchen & Dining',
        4 => 'Bedroom & Bath',
        5 => 'Home Office / Workstation',
        6 => 'Mobile & On-the-Go',
        7 => 'Studio & Creative Work',
        8 => 'Network & Tech Infrastructure',
        9 => 'Personal Care & Wardrobe',
        10 => 'Leisure & Recreation',
    ];

    public const TYPES = [
        1 => 'Home Improvement & Renovation',
        2 => 'Home Appliances',
        3 => 'Furniture & Home Decor',
        4 => 'Electronics & Tech',
        5 => 'Fashion & Lifestyle',
        6 => 'Hobbies & Creative',
        7 => 'Experiences & Travel',
        8 => 'Gifts & Occasions',
        9 => 'Everyday & Maintenance',
    ];

    // Accessor for Category Label ($item->category_label)
    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Uncategorized';
    }

    // Accessor for Type Label ($item->type_label)
    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? 'Unknown';
    }

    // Human-friendly, context-aware date attribute
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
    
}
