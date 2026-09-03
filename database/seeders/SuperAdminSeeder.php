<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Seed the default super-admin account.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@pageant.com',
                'password' => 'password',
                'role' => 'super-admin',
                'is_active' => true,
            ]
        );
    }
}
