<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('admin.email')],
            [
                'name' => 'Tabiana',
                'role' => 'admin',
                'password' => Hash::make('phmhcc'),
                'email_verified_at' => now(),
            ]
        );
    }
}
