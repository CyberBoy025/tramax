<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        Storage::fake('public');
    }

    public function test_admin_can_upload_an_image_for_an_allowed_context(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::AR_MANAGER));

        $response = $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg'),
            'context' => 'artists',
        ]);

        $response->assertCreated()->assertJsonStructure(['data' => ['url', 'path']]);
        Storage::disk('public')->assertExists($response->json('data.path'));
    }

    public function test_ar_manager_cannot_upload_into_products_context(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::AR_MANAGER));

        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('cover.jpg', 100, 'image/jpeg'),
            'context' => 'products',
        ])->assertStatus(403);
    }

    public function test_content_manager_can_upload_into_products_and_news_only(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::CONTENT_MANAGER));

        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
            'context' => 'products',
        ])->assertCreated();

        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('b.jpg', 100, 'image/jpeg'),
            'context' => 'artists',
        ])->assertStatus(403);
    }

    public function test_super_admin_can_upload_into_any_context(): void
    {
        Sanctum::actingAs($this->superAdmin());

        foreach (['artists', 'releases', 'products', 'news'] as $context) {
            $this->postJson('/api/v1/admin/uploads', [
                'file' => UploadedFile::fake()->create("{$context}.jpg", 100, 'image/jpeg'),
                'context' => $context,
            ])->assertCreated();
        }
    }

    public function test_finance_and_management_cannot_upload_at_all(): void
    {
        foreach ([Role::FINANCE, Role::MANAGEMENT] as $roleName) {
            Sanctum::actingAs($this->userWithRole($roleName));
            $this->postJson('/api/v1/admin/uploads', [
                'file' => UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
                'context' => 'artists',
            ])->assertStatus(403);
        }
    }

    public function test_non_image_files_are_rejected(): void
    {
        Sanctum::actingAs($this->superAdmin());

        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
            'context' => 'artists',
        ])->assertStatus(422);
    }

    public function test_oversized_files_are_rejected(): void
    {
        Sanctum::actingAs($this->superAdmin());

        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('huge.jpg', 100, 'image/jpeg')->size(6000),
            'context' => 'artists',
        ])->assertStatus(422);
    }

    public function test_unknown_context_is_rejected(): void
    {
        Sanctum::actingAs($this->superAdmin());

        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
            'context' => 'not-a-real-context',
        ])->assertStatus(422);
    }

    public function test_artist_can_upload_via_the_portal_route(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::ARTIST));

        $response = $this->postJson('/api/v1/portal/uploads', [
            'file' => UploadedFile::fake()->create('profile.jpg', 100, 'image/jpeg'),
            'context' => 'artists',
        ]);

        $response->assertCreated();
        Storage::disk('public')->assertExists($response->json('data.path'));
    }

    public function test_artist_cannot_reach_the_admin_upload_route(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::ARTIST));

        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
            'context' => 'artists',
        ])->assertStatus(403);
    }

    public function test_admin_roles_cannot_reach_the_portal_upload_route(): void
    {
        Sanctum::actingAs($this->superAdmin());

        $this->postJson('/api/v1/portal/uploads', [
            'file' => UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
            'context' => 'artists',
        ])->assertStatus(403);
    }

    public function test_unauthenticated_upload_is_rejected(): void
    {
        $this->postJson('/api/v1/admin/uploads', [
            'file' => UploadedFile::fake()->create('a.jpg', 100, 'image/jpeg'),
            'context' => 'artists',
        ])->assertStatus(401);
    }
}
