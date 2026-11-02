<?php

namespace Database\Seeders;

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
            ['name' => 'deacon', 'description' => 'Deacon'],
            ['name' => 'elder', 'description' => 'Elder'],
            ['name' => 'department_leader', 'description' => 'Department Leader'],
            ['name' => 'treasurer', 'description' => 'Treasurer']
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], ['description' => $role['description']]);
        }
    }
}
