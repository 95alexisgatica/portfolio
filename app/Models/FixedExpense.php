<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FixedExpense extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'detail',
        'payment_type',
        'notes',
        'amount',
        'starts_at',
        'ends_at',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'starts_at' => 'date:Y-m-d',
            'ends_at' => 'date:Y-m-d',
            'active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(FixedExpenseOccurrence::class);
    }
}
