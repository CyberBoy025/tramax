<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLogEntry;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_admin_create_is_logged(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);

        $this->postJson('/api/v1/admin/products', ['title' => 'Test Hoodie', 'status' => 'Draft'])
            ->assertCreated();

        $this->assertDatabaseHas('audit_log_entries', [
            'user_id' => $admin->id,
            'action' => 'created',
            'entity_type' => 'Product',
        ]);
    }

    public function test_admin_update_is_logged_with_only_changed_fields(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);

        $productId = $this->postJson('/api/v1/admin/products', ['title' => 'Test Cap', 'status' => 'Draft'])
            ->json('data.id');

        $this->patchJson("/api/v1/admin/products/{$productId}", ['status' => 'Published'])->assertOk();

        $entry = AuditLogEntry::query()->where('action', 'updated')->where('entity_id', $productId)->firstOrFail();
        $this->assertSame(['status' => 'Published'], $entry->metadata);
    }

    public function test_admin_delete_is_logged(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);

        $productId = $this->postJson('/api/v1/admin/products', ['title' => 'Test Tee', 'status' => 'Draft'])
            ->json('data.id');

        $this->deleteJson("/api/v1/admin/products/{$productId}")->assertOk();

        $this->assertDatabaseHas('audit_log_entries', [
            'action' => 'deleted',
            'entity_type' => 'Product',
            'entity_id' => $productId,
        ]);
    }

    public function test_unauthenticated_public_submission_is_not_logged(): void
    {
        $this->postJson('/api/v1/applications', [
            'full_name' => 'Jane Doe',
            'artist_name' => 'Jane D',
            'email' => 'jane@example.test',
        ])->assertCreated();

        $this->assertDatabaseCount('audit_log_entries', 0);
    }

    public function test_password_changes_are_redacted_to_a_flag(): void
    {
        $admin = $this->superAdmin();
        Sanctum::actingAs($admin);
        $financeRole = Role::query()->where('name', Role::FINANCE)->firstOrFail();

        $userId = $this->postJson('/api/v1/admin/users', [
            'name' => 'Audit Password Test',
            'email' => 'audit-password@tramax.test',
            'password' => 'password123',
            'role_id' => $financeRole->id,
        ])->json('data.id');

        $entry = AuditLogEntry::query()
            ->where('entity_type', 'User')->where('entity_id', $userId)->where('action', 'created')
            ->firstOrFail();

        $this->assertArrayNotHasKey('password', $entry->metadata);
        $this->assertTrue($entry->metadata['password_changed']);
    }

    public function test_rbac_matches_matrix_for_read(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::MANAGEMENT));
        $this->getJson('/api/v1/admin/audit-log')->assertOk();

        Sanctum::actingAs($this->userWithRole(Role::AR_MANAGER));
        $this->getJson('/api/v1/admin/audit-log')->assertStatus(403);
    }
}
