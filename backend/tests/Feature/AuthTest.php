<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    public function test_login_with_valid_credentials_issues_a_token(): void
    {
        $this->seedRoles();
        $user = $this->superAdmin();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonPath('data.user.role', 'Super Administrator')
            ->assertJsonStructure(['data' => ['token', 'user' => ['id', 'name', 'email', 'role']]]);
    }

    public function test_login_with_wrong_password_is_rejected(): void
    {
        $this->seedRoles();
        $user = $this->superAdmin();

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_with_unknown_email_is_rejected(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'nobody@tramax.test',
            'password' => 'password',
        ]);

        $response->assertStatus(422);
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/v1/auth/me')->assertStatus(401);
    }

    public function test_me_returns_the_authenticated_user(): void
    {
        $this->seedRoles();
        $user = $this->superAdmin();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);
    }

    public function test_logout_revokes_the_token(): void
    {
        $this->seedRoles();
        $user = $this->superAdmin();

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $token = $login->json('data.token');
        $this->assertSame(1, PersonalAccessToken::count());

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        // The token row is gone — that's what actually makes it unusable in
        // production. A second request re-authenticating with it in the same
        // test would still succeed here regardless, because Laravel's auth
        // guard memoizes the resolved user on a container-scoped singleton
        // that a real client's next HTTP connection never shares — forcing a
        // fresh guard is what makes that in-test check meaningful too.
        $this->assertSame(0, PersonalAccessToken::count());
        Auth::forgetGuards();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/auth/me')
            ->assertStatus(401);
    }
}
