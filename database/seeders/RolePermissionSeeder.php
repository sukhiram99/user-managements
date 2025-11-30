<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions (safe: won't duplicate)
        $permissions = [
            'view-users'       => 'View Users',
            'create-users'     => 'Create Users',
            'edit-users'       => 'Edit Users',
            'delete-users'     => 'Delete Users',
            'view-roles'       => 'View Roles',
            'create-roles'     => 'Create Roles',
            'edit-roles'       => 'Edit Roles',
            'delete-roles'     => 'Delete Roles',
            'view-dashboard'   => 'View Dashboard',
            
            'view-permissions'   => 'View Permissions',
            'create-permissions'   => 'Create Permissions',
            'edit-permissions'   => 'Edit Permissions',
            'delete-permissions'   => 'Delete Permissions',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::updateOrCreate(
                ['slug' => $slug],              // Unique key
                ['name' => $name]
            );
        }

        // 2. Create Roles (safe: won't duplicate)
        $manager = Role::updateOrCreate(
            ['slug' => 'manager'],
            [
                'name'        => 'Manager',
                'description' => 'Full access to user management',
            ]
        );

        $userRole = Role::updateOrCreate(
            ['slug' => 'user'],
            [
                'name'        => 'User',
                'description' => 'Regular user - limited access',
            ]
        );

        // 3. Sync Permissions to Roles (no duplicates, clean every time)
        $manager->permissions()->sync(
            Permission::whereIn('slug', [
                'view-users', 'create-users', 'edit-users', 'delete-users',
                'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
                'view-dashboard'
            ])->pluck('id')->toArray()
        );

        $userRole->permissions()->sync(
            Permission::whereIn('slug', ['view-dashboard', 'view-users'])
                ->pluck('id')->toArray()
        );

        // 4. Create or Update Manager User (email-based, safe)
        $managerUser = User::updateOrCreate(
            ['email' => 'manager@gmail.com'],
            [
                'name'              => 'Manager User',
                'password'          => Hash::make('Manager@123'), // change in production!
                'email_verified_at' => now(),
            ]
        );

        // Assign Manager role
        $managerUser->roles()->sync($manager->id);

        // Optional: Create a regular user
        $regularUser = User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name'              => 'Test User',
                'password'          => Hash::make('User@123'), 
                'email_verified_at' => now(),
            ]
        );

        $regularUser->roles()->sync($userRole->id);

        $this->command->info('Roles, permissions, and users seeded successfully!');
    }
}