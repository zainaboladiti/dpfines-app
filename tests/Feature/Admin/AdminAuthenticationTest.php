<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\GlobalFine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthenticated users cannot access admin panel
     */
    public function test_unauthenticated_users_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $this->assertNotEquals(200, $response->status());
    }

    /**
     * Test non-admin users cannot access admin panel
     */
    public function test_non_admin_users_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $this->assertEquals(403, $response->status());
    }

    /**
     * Test admin users can login successfully
     */
    public function test_admin_login_success(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'password' => bcrypt('TestPassword123!'),
            'is_admin' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'TestPassword123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Test non-admin users cannot login even with valid credentials
     */
    public function test_non_admin_login_fails(): void
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('TestPassword123!'),
            'is_admin' => false,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'user@test.com',
            'password' => 'TestPassword123!',
        ]);

        $this->assertGuest();
    }

    /**
     * Test invalid credentials fail
     */
    public function test_invalid_credentials_fail(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@test.com',
            'is_admin' => true,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@test.com',
            'password' => 'WrongPassword123!',
        ]);

        $this->assertGuest();
    }

    /**
     * Test admin logout
     */
    public function test_admin_logout(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
