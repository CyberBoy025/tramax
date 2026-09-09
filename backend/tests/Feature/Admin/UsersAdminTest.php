<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

class UsersAdminTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_super_admin_can_create_a_user(): void
    {
        Sanctum::actingAs($this->superAdmin());
        $financeRole = Role::query()->where('name', Role::FINANCE)->firstOrFail();

        $response = $this->postJson('/api/v1/admin/users', [
            'name' => 'New Finance Hire',
            'email' => 'new-finance@tramax.test',
            'password' => 'password123',
            'role_id' => $financeRole->id,
        ]);

        $response->assertCreated()->assertJsonPath('data.email', 'new-finance@tramax.test');
        $this->assertDatabaseHas('users', ['email' => 'new-finance@tramax.test', 'role_id' => $financeRole->id]);
    }

    public function test_password_is_hashed_on_create(): void
    {
        Sanctum::actingAs($this->superAdmin());
        $financeRole = Role::query()->where('name', Role::FINANCE)->firstOrFail();

        $this->postJson('/api/v1/admin/users', [
            'name' => 'Hash Check',
            'email' => 'hash-check@tramax.test',
            'password' => 'password123',
            'role_id' => $financeRole->id,
        ])->assertCreated();

        $stored = User::where('email', 'hash-check@tramax.test')->firstOrFail();
        $this->assertNotEquals('password123', $stored->password);
        $this->assertTrue(Hash::check('password123', $stored->password));
    }

    public function test_cannot_change_own_role(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/v1/admin/users/{$admin->id}", [
            'role_id' => Role::query()->where('name', Role::MANAGEMENT)->firstOrFail()->id,
        ]);

        $response->assertStatus(422)->assertJsonPath('message', 'You cannot change your own role.');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role_id' => $admin->role_id]);
    }

    public function test_cannot_suspend_own_account(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/v1/admin/users/{$admin->id}", ['status' => 'Suspended']);

        $response->assertStatus(422)->assertJsonPath('message', 'You cannot suspend your own account.');
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'status' => 'Active']);
    }

    public function test_cannot_delete_own_account(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/v1/admin/users/{$admin->id}");

        $response->assertStatus(422)->assertJsonPath('message', 'You cannot delete your own account.');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_super_admin_can_change_another_users_role(): void
    {
        Sanctum::actingAs($this->superAdmin());
        $other = $this->userWithRole(Role::FINANCE);
        $managementRole = Role::query()->where('name', Role::MANAGEMENT)->firstOrFail();

        $this->patchJson("/api/v1/admin/users/{$other->id}", ['role_id' => $managementRole->id])
            ->assertOk()
            ->assertJsonPath('data.role.name', Role::MANAGEMENT);
    }

    public function test_super_admin_can_delete_another_user(): void
    {
        Sanctum::actingAs($this->superAdmin());
        $other = $this->userWithRole(Role::FINANCE);

        $this->deleteJson("/api/v1/admin/users/{$other->id}")->assertOk();
        $this->assertDatabaseMissing('users', ['id' => $other->id]);
    }

    public function test_blank_password_on_update_leaves_password_unchanged(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);
        $other = $this->userWithRole(Role::FINANCE);
        $originalHash = $other->password;

        $this->patchJson("/api/v1/admin/users/{$other->id}", [
            'name' => 'Renamed Finance User',
            'password' => '',
        ])->assertOk();

        $this->assertEquals($originalHash, $other->fresh()->password);
    }
}
