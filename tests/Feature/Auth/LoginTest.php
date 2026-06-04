<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'username' => 'admin_test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_authenticate_using_username(): void
    {
        $user = User::factory()->create([
            'username' => 'admin_test',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'admin_test',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@test.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['login']);
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'login' => 'admin@test.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['login' => 'Your account is deactivated.']);
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
