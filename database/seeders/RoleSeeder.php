<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $roles = [
        'owner',
        'admin',
        'warehouse',
        'finance',
        'cs',
        'customer',
    ];

    public function run(): void
    {
        foreach ($this->roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }
}
