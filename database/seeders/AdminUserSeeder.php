<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->updateOrCreate(
            ['email' => 'owner@carinasmartcup.test'],
            [
                'name' => 'Owner Carina',
                'password' => Hash::make('password'),
                'whatsapp' => '6281111111111',
                'email_verified_at' => now(),
            ],
        );
        $owner->syncRoles(['owner']);

        $finance = User::query()->updateOrCreate(
            ['email' => 'finance@carinasmartcup.test'],
            [
                'name' => 'Finance Carina',
                'password' => Hash::make('password'),
                'whatsapp' => '6281222222222',
                'email_verified_at' => now(),
            ],
        );
        $finance->syncRoles(['finance']);

        $warehouse = User::query()->updateOrCreate(
            ['email' => 'warehouse@carinasmartcup.test'],
            [
                'name' => 'Warehouse Carina',
                'password' => Hash::make('password'),
                'whatsapp' => '6281333333333',
                'email_verified_at' => now(),
            ],
        );
        $warehouse->syncRoles(['warehouse']);

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@carinasmartcup.test'],
            [
                'name' => 'Admin Carina',
                'password' => Hash::make('password'),
                'whatsapp' => '6281444444444',
                'email_verified_at' => now(),
            ],
        );
        $admin->syncRoles(['admin']);
    }
}
