<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleAndUserSeeder extends Seeder
{
    public function run()
    {
        // Roles ("De Canela?" XD)
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Create an admin user
        User::firstOrCreate(
            ['email' => 'admin@sciencesport.org'],
            [
                'name' => 'Admin Science of Sport',
                'password' => Hash::make('password123'), // Bruh..... Super Secure :v
                'role_id' => $adminRole->id,
            ]
        );

        // Normal user
        User::firstOrCreate(
            ['email' => 'user@sciencesport.org'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password123'),
                'role_id' => $userRole->id,
            ]
        );
    }
}
