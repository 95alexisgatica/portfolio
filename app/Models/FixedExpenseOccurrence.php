<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedExpenseOccurrence extends Model
{
    protected $fillable = [
        'fixed_expense_id',
        'year',
        'month',
        'amount',
        'detail',
        'payment_type',
        'notes',
        'status',
        'excluded',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
            'amount' => 'decimal:2',
            'excluded' => 'boolean',
        ];
    }

    public function fixedExpense(): BelongsTo
    {
        return $this->belongsTo(FixedExpense::class);
    }
}
