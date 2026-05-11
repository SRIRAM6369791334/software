<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createAdmin()
    {
        $user = \App\Models\User::factory()->create();
        $adminRole = \App\Models\Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['description' => 'Admin', 'is_system' => true]
        );
        $user->assignRole($adminRole);

        return $user;
    }
}
