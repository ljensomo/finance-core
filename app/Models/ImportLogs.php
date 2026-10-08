<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLogs extends Model
{
    protected $table = 'import_logs';

    protected $fillable = [
        'user_id',
        'total_rows',
        'rows_imported',
        'rows_failed',
        'last_row_number',
    ];

    protected $appends = [
        'formatted_created_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getFormattedCreatedAtAttribute(): string{
        if (!$this->created_at) {
            return '';
        }

        if ($this->created_at->isToday()) {
            return 'Today';
        }

        if ($this->created_at->isYesterday()) {
            return 'Yesterday';
        }

        return $this->created_at->format('M d, Y');
    }

}
