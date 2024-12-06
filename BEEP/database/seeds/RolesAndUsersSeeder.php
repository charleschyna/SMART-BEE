<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Call the updateRoles method from the Role model to create the roles and attach permissions
        Role::updateRoles();

        // Creating Superadmin User
        $superAdminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'), // Ensure this is a strong password
            'api_token' => \Str::random(60),
        ]);

        // Assign the 'superadmin' role to the created user
        $superAdminUser->attachRole('superadmin');

        // Creating Admin User
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'api_token' => \Str::random(60),
        ]);

        // Assign the 'admin' role to the created user
        $adminUser->attachRole('admin');
    }
}

