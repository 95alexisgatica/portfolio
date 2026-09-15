<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'google_id', 'nickname', 'avatar_path', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        return Storage::disk('public')->url($this->avatar_path);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function seedDefaultCategories(): void
    {
        if ($this->categories()->exists()) {
            return;
        }

        $defaults = [
            ['🍽️ Alimentación', '#d94f4f'],
            ['🚌 Transporte', '#3f78b5'],
            ['🏠 Hogar', '#6b9b45'],
            ['💊 Salud', '#a65d9b'],
            ['🎬 Entretenimiento', '#d88732'],
            ['💡 Servicios', '#4e9b75'],
            ['🛒 Supermercado', '#c05c82'],
            ['💰 Ahorro', '#3c8b78'],
        ];

        foreach ($defaults as [$name, $color]) {
            $this->categories()->create([
                'name' => $name,
                'color' => $color,
            ]);
        }
    }
}
