<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportedTransactionLogs extends Model
{
    protected $fillable = [
        'import_log_id',
        'row_number',
        'transaction_timestamp',
        'status',
        'transaction_id',
        'error_message',
    ];
}
