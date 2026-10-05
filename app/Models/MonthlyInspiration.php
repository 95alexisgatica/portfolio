<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class MonthlyInspiration extends Model
{
    protected $fillable = ['user_id', 'year', 'month', 'path', 'image_url', 'original_name'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'month' => 'integer',
        ];
    }

    public function imageUrl(): ?string
    {
        if ($this->image_url) {
            return $this->image_url;
        }

        return $this->path ? Storage::disk('public')->url($this->path) : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
