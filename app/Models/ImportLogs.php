<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLogs extends Model
{
    protected $fillable = [
        'user_id',
        'total_rows',
        'rows_imported',
        'rows_failed',
        'last_row_number',
    ];
}
