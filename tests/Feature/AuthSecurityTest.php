<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_quick_login_is_accessible_in_local_environment(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'status' => 'active',
        ]);

        $response = $this->get(route('auth.quick-login', 'admin'));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_suspended_user_cannot_access_protected_routes_via_middleware(): void
    {
        $suspendedUser = User::factory()->create([
            'role' => 'student',
            'status' => 'suspended',
        ]);

        $response = $this->actingAs($suspendedUser)->get(route('student.dashboard'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_suspended_admin_cannot_quick_login(): void
    {
        User::factory()->create([
            'role' => 'admin',
            'status' => 'suspended',
        ]);

        $response = $this->get(route('auth.quick-login', 'admin'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
