<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator'],
            ['name' => 'pastor', 'description' => 'Pastor'],
            ['name' => 'elder', 'description' => 'Elder'],
            ['name' => 'department_leader', 'description' => 'Department Leader'],
            ['name' => 'treasurer', 'description' => 'Treasurer'],
            ['name' => 'decon', 'description' => 'Decon'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
