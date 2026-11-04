<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_admin_can_login_and_redirect_to_admin_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'ADMIN']);
        $admin = User::factory()->create();
        $admin->assignRole('ADMIN');

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $dashboardResponse = $this->actingAs($admin)->get('/dashboard');
        $dashboardResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_owner_can_login_and_redirect_to_owner_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'OWNER']);
        $owner = User::factory()->create();
        $owner->assignRole('OWNER');

        $response = $this->post('/login', [
            'email' => $owner->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $dashboardResponse = $this->actingAs($owner)->get('/dashboard');
        $dashboardResponse->assertRedirect(route('owner.dashboard'));
    }

    public function test_kasir_can_login_and_redirect_to_kasir_dashboard(): void
    {
        Role::firstOrCreate(['name' => 'KASIR']);
        $kasir = User::factory()->create();
        $kasir->assignRole('KASIR');

        $response = $this->post('/login', [
            'email' => $kasir->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $dashboardResponse = $this->actingAs($kasir)->get('/dashboard');
        $dashboardResponse->assertRedirect(route('kasir.dashboard'));
    }
}
