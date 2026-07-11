<?php

namespace Database\Seeders;

use App\Domains\Account\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin POS',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
