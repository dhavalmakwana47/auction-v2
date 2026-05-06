<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole       = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'RA']);
        

        // Define permissions per module
        $permissions = [
            // Dashboard
            'view dashboard',
            // RA Dashboard
            'view ra-dashboard',
            // Users
            'view users', 'create users', 'edit users', 'delete users',
            // Roles
            'view roles', 'create roles', 'edit roles', 'delete roles',
            // Logs
            'view logs',
            // Auctions
            'view auctions', 'create auctions', 'edit auctions', 'delete auctions',
            // NPV Categories
            'view npv-categories', 'create npv-categories', 'edit npv-categories', 'delete npv-categories',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Admin — all permissions
        $adminRole->syncPermissions($permissions);


        // RA role — ra dashboard only
        Role::findByName('RA')->syncPermissions(['view ra-dashboard']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@auction.com'],
            [
                'name'      => 'Admin',
                'password'  => Hash::make('Admin@1234'),
                'is_active' => 1,
            ]
        );

        $admin->assignRole('admin');
    }
}
