<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'alexisgatica95@gmail.com'],
            [
                'name' => 'Alexis',
                'nickname' => 'Alexis',
                'password' => null,
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ],
        );

        $admin->seedDefaultCategories();
    }
}
