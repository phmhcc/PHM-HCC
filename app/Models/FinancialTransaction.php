<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'transaction_date',
        'type',
        'category',
        'description',
        'amount',
        'reference',
        'receipt_path',
        'recorded_by',
    ];

    protected $casts = ['transaction_date' => 'date', 'amount' => 'decimal:2'];
}
