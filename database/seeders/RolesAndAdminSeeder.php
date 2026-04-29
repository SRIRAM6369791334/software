<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // ── System Roles ────────────────────────────────────────────────────────
        $systemRoles = [
            ['name' => 'viewer',  'description' => 'Can view all reports and dashboards', 'is_system' => true],
            ['name' => 'staff',   'description' => 'Can manage master records and purchases', 'is_system' => true],
            ['name' => 'manager', 'description' => 'Can manage billing, payments, and expenses', 'is_system' => true],
            ['name' => 'admin',   'description' => 'Full system access including user management', 'is_system' => true],
        ];

        foreach ($systemRoles as $roleData) {
            Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        }

        // ── Default Admin User ───────────────────────────────────────────────────
        $admin = User::firstOrCreate(
            ['email' => 'admin@poultrypro.local'],
            [
                'name'     => 'System Admin',
                'password' => Hash::make('Admin@1234'),
            ]
        );

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            UserRole::create(['user_id' => $admin->id, 'role_id' => $adminRole->id]);
        }

        $this->command->info('✅ 4 system roles created. Admin user: admin@poultrypro.local / Admin@1234');
    }
}
