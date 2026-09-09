<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

// Each test method here gets a fresh application instance (and therefore a
// fresh "array" cache — see phpunit.xml's CACHE_STORE), so rate-limit state
// never leaks between tests without needing to clear it explicitly.
class RateLimitingTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    public function test_public_form_endpoint_is_throttled_after_five_requests_per_minute(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson('/api/v1/contact', [
                'name' => 'Jane Doe',
                'email' => 'jane@example.test',
                'message' => 'Hello',
            ]);
            $response->assertStatus(201);
        }

        $this->postJson('/api/v1/contact', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.test',
            'message' => 'Hello',
        ])->assertStatus(429);
    }

    public function test_throttle_is_shared_across_the_public_form_endpoints(): void
    {
        // Same limiter name ("public-forms"), keyed by IP only — five
        // requests split across different endpoints should still trip it,
        // not reset per-route.
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/contact', [
                'name' => 'Jane Doe', 'email' => 'jane@example.test', 'message' => 'Hello',
            ])->assertStatus(201);
        }

        $this->postJson('/api/v1/partners', [
            'organization_name' => 'Acme', 'contact_person' => 'Sam', 'email' => 'sam@example.test',
        ])->assertStatus(429);
    }

    public function test_login_is_throttled_after_five_attempts_per_minute(): void
    {
        $this->seedRoles();
        $user = $this->superAdmin();

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(429);
    }

    public function test_login_throttle_does_not_block_a_different_email_from_the_same_ip(): void
    {
        $this->seedRoles();
        $user = $this->superAdmin();
        $other = $this->userWithRole(Role::MANAGEMENT);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ])->assertStatus(422);
        }

        // Different email, same IP — the "login" limiter keys on IP+email
        // together, so this should NOT be throttled by the attempts above.
        $this->postJson('/api/v1/auth/login', [
            'email' => $other->email,
            'password' => 'password',
        ])->assertOk();
    }
}
