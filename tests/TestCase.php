<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function createAdmin()
    {
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $user = \App\Models\User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }
}
