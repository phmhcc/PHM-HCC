<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        
    User::factory()->create([
        'name' => 'Tabiana',
        'email' => 'tabianavitus@gmail.com',
        'password' => Hash::make('phmhcc')
    ]);

}
}
