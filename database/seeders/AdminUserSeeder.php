<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('admin.password');

        if (! is_string($password) || $password === '') {
            throw new RuntimeException('Set ADMIN_PASSWORD to your chosen non-empty password in .env, then run php artisan config:clear and try seeding again.');
        }

        $admin = User::firstOrCreate(
            ['email' => 'tabianavitus@gmail.com'],
            [
                'name' => 'Tabiana',
                'role' => 'admin',
                'password' => Hash::make('phmhcc'),
                'email_verified_at' => now(),
            ]
        );

        if ($admin->name !== 'Tabiana' || $admin->role !== 'admin') {
            $admin->update(['name' => 'Tabiana', 'role' => 'admin']);
        }
    }
}
