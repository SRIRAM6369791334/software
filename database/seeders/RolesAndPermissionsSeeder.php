<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'view customers', 'create customers', 'edit customers', 'delete customers',
            'view dealers', 'create dealers', 'edit dealers', 'delete dealers',
            'view vendors', 'create vendors', 'edit vendors', 'delete vendors',
            'view bills', 'create bills', 'edit bills', 'delete bills',
            'view purchases', 'create purchases', 'edit purchases', 'delete purchases',
            'view payments', 'create payments', 'edit payments', 'delete payments',
            'view expenses', 'create expenses', 'edit expenses', 'delete expenses',
            'view emis', 'create emis', 'edit emis', 'delete emis',
            'view reports', 'view profit dashboard',
            'manage users', 'manage roles'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and Assign Permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view customers', 'create customers', 'edit customers',
            'view dealers', 'create dealers', 'edit dealers',
            'view vendors', 'create vendors', 'edit vendors',
            'view bills', 'create bills', 'edit bills',
            'view purchases', 'create purchases', 'edit purchases',
            'view payments', 'create payments', 'edit payments',
            'view expenses', 'create expenses', 'edit expenses',
            'view emis', 'create emis', 'edit emis',
            'view reports', 'view profit dashboard'
        ]);

        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $staffRole->givePermissionTo([
            'view bills', 'create bills',
            'view customers'
        ]);

        // Create Default Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@poultry.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@1234')
            ]
        );
        $admin->assignRole('admin');
    }
}
