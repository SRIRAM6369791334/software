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
            'manage users', 'manage roles',
            'view stock', 'create stock', 'edit stock', 'delete stock',
            'view routes', 'create routes', 'edit routes', 'delete routes',
            'view batches', 'create batches', 'edit batches', 'delete batches',
            'mark delivery status'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ROLE 1 - Admin: Full access
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // ROLE 2 - Accountant: Billing, Payment, Reports, Profit, Expenses. NO delete, NO user management.
        $accountantRole = Role::firstOrCreate(['name' => 'accountant']);
        $accountantRole->givePermissionTo([
            'view customers', 'view dealers', 'view vendors',
            'view bills', 'create bills', 'edit bills',
            'view payments', 'create payments', 'edit payments',
            'view expenses', 'create expenses', 'edit expenses',
            'view reports', 'view profit dashboard',
            'view stock'
        ]);

        // ROLE 3 - Delivery Staff: View Route, view Bills, mark delivery status. NO financial data.
        $deliveryStaffRole = Role::firstOrCreate(['name' => 'delivery_staff']);
        $deliveryStaffRole->givePermissionTo([
            'view routes',
            'view bills',
            'mark delivery status'
        ]);

        // ROLE 4 - Data Entry Operator: Purchase Entry, Stock Entry, Master (Add/Edit only). NO delete.
        $dataEntryRole = Role::firstOrCreate(['name' => 'data_entry']);
        $dataEntryRole->givePermissionTo([
            'view customers', 'create customers', 'edit customers',
            'view dealers', 'create dealers', 'edit dealers',
            'view vendors', 'create vendors', 'edit vendors',
            'view purchases', 'create purchases', 'edit purchases',
            'view stock', 'create stock', 'edit stock',
            'view batches', 'create batches', 'edit batches'
        ]);

        // Create Default Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@poultry.com'],
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'password' => Hash::make('Admin@1234'),
                'is_active' => true
            ]
        );
        $admin->syncRoles(['admin']);
    }
}
