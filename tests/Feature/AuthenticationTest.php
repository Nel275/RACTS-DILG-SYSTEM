<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_users_can_authenticate_with_correct_credentials(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->post('/login', [
            'username' => 'testadmin',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_users_cannot_authenticate_with_the_wrong_password(): void
    {
        User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->post('/login', [
            'username' => 'testadmin',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    public function test_guests_are_redirected_to_login_from_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_log_out(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'username' => 'testadmin',
            'password' => 'password123',
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
