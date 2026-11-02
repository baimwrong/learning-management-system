<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
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

    public function test_instructor_registration_is_set_to_pending_approval(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Dr. Candidate',
            'email' => 'candidate@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'instructor',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('info');

        $user = User::where('email', 'candidate@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('instructor', $user->role);
        $this->assertEquals('pending', $user->status);
        $this->assertGuest(); // Not automatically logged in
    }

    public function test_student_registration_is_active_immediately(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Active Student',
            'email' => 'student.active@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role' => 'student',
        ]);

        $response->assertRedirect(route('student.dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'student.active@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('student', $user->role);
        $this->assertEquals('active', $user->status);
    }

    public function test_admin_cannot_delete_instructor_with_active_courses(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $instructor = User::factory()->create(['role' => 'instructor', 'status' => 'active']);

        \App\Models\Course::create([
            'title' => 'Critical Architecture Course',
            'slug' => 'critical-architecture-course',
            'instructor_id' => $instructor->id,
            'is_published' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $instructor));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $instructor->id]);
    }

    public function test_user_can_view_forgot_password_screen(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertSee('Reset Password');
        $response->assertSee('Send Password Reset Link');
    }

    public function test_user_can_request_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'status' => 'active',
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'learner@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_user_can_view_reset_password_screen_with_token(): void
    {
        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'status' => 'active',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]));

        $response->assertStatus(200);
        $response->assertSee('Set New Password');
        $response->assertSee($user->email);
    }

    public function test_user_can_reset_password_with_valid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'password' => Hash::make('OldPassword123!'),
            'status' => 'active',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->fresh()->password));
    }

    public function test_user_cannot_reset_password_with_invalid_token(): void
    {
        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'password' => Hash::make('OldPassword123!'),
            'status' => 'active',
        ]);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token-12345',
            'email' => $user->email,
            'password' => 'NewSecurePassword123!',
            'password_confirmation' => 'NewSecurePassword123!',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertTrue(Hash::check('OldPassword123!', $user->fresh()->password));
    }

    public function test_404_error_page_renders_custom_view(): void
    {
        $response = $this->get('/non-existent-lms-path-xyz');

        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Resource Not Found');
    }

    public function test_403_error_page_renders_custom_view(): void
    {
        $student = User::factory()->create([
            'role' => 'student',
            'status' => 'active',
        ]);

        $response = $this->actingAs($student)->get(route('admin.dashboard'));

        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Access Denied');
    }
}
