<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_role_level(): void
    {
        $user = User::factory()->create();

        // Needs to have Roles in the database.
        // Assuming spatie/laravel-permission creates roles like 'admin', 'viewer'
        \Spatie\Permission\Models\Role::create(['name' => 'viewer']);
        \Spatie\Permission\Models\Role::create(['name' => 'admin']);

        // Default no roles -> level 0
        $this->assertEquals(0, $user->getRoleLevel());

        // Assign viewer -> level 1
        $user->assignRole('viewer');
        $this->assertEquals(1, $user->getRoleLevel());

        // Assign admin -> level 4 (highest)
        $user->assignRole('admin');
        $this->assertEquals(4, $user->getRoleLevel());
    }
}
