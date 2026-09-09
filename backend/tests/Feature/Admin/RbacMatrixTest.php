<?php

namespace Tests\Feature\Admin;

use App\Models\ArtistProfile;
use App\Models\Event;
use App\Models\LicensingRequest;
use App\Models\NewsPost;
use App\Models\Product;
use App\Models\Release;
use App\Models\RightsRecord;
use App\Models\Role;
use App\Models\RoyaltyStatement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

// Encodes discovery.md §3's RBAC matrix directly, row by row — this is the
// automated version of the curl matrix checks run by hand for every module
// this build. A change here should only ever follow a documented change to
// that table (per its own stated policy), never the other way around.
//
// Two real bugs were caught building this suite and fixed in routes/api.php
// alongside it: Content Manager was missing Read on Releases (a separate
// matrix row from Artist Management, previously sharing its tier), and
// every "Manage" role (A&R, Finance, Content Manager where applicable) had
// DELETE bundled into its write tier even though the matrix legend defines
// Manage as "no destructive delete" — only Super Admin should reach any
// DELETE admin route unless the matrix gives another role Full.
class RbacMatrixTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    private array $deniedByDefault = [
        Role::FINANCE,
        Role::CONTENT_MANAGER,
        Role::ARTIST,
        Role::PARTNER,
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    private function assertReadAccess(string $uri, array $allowed): void
    {
        foreach (Role::ALL as $roleName) {
            $user = $this->userWithRole($roleName);
            Sanctum::actingAs($user);

            $response = $this->getJson($uri);

            if (in_array($roleName, $allowed, true)) {
                $response->assertOk();
            } else {
                $response->assertStatus(403);
            }
        }
    }

    private function assertSuperAdminOnlyDelete(string $uri): void
    {
        foreach (Role::ALL as $roleName) {
            if ($roleName === Role::SUPER_ADMIN) {
                continue;
            }
            $user = $this->userWithRole($roleName);
            Sanctum::actingAs($user);

            $this->deleteJson($uri)->assertStatus(403);
        }
    }

    public function test_artist_management_and_applications_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/applications', [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER]);
        $this->assertReadAccess('/api/v1/admin/artists', [Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER]);

        $artist = ArtistProfile::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/artists/{$artist->id}");
    }

    public function test_music_catalogue_releases_rbac(): void
    {
        // Its own matrix row — Content Manager gets Read here, unlike Artist Management above.
        $this->assertReadAccess('/api/v1/admin/releases', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::CONTENT_MANAGER,
        ]);

        $release = Release::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/releases/{$release->id}");
    }

    public function test_rights_management_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/rights-records', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE,
        ]);

        $right = RightsRecord::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/rights-records/{$right->id}");
    }

    public function test_royalty_management_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/royalty-statements', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE,
        ]);

        $statement = RoyaltyStatement::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/royalty-statements/{$statement->id}");
    }

    public function test_licensing_requests_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/licensing-requests', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE,
        ]);

        $request = LicensingRequest::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/licensing-requests/{$request->id}");
    }

    public function test_events_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/events', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::CONTENT_MANAGER,
        ]);

        $event = Event::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/events/{$event->id}");
    }

    public function test_store_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/products', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::CONTENT_MANAGER,
        ]);

        $product = Product::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/products/{$product->id}");
    }

    public function test_content_management_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/news', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::CONTENT_MANAGER,
        ]);

        $post = NewsPost::factory()->create();
        $this->assertSuperAdminOnlyDelete("/api/v1/admin/news/{$post->id}");
    }

    public function test_partnerships_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/partners', [Role::SUPER_ADMIN, Role::MANAGEMENT]);
    }

    public function test_users_and_roles_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/users', [Role::SUPER_ADMIN]);
        $this->assertReadAccess('/api/v1/admin/roles', [Role::SUPER_ADMIN]);
    }

    public function test_audit_log_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/audit-log', [Role::SUPER_ADMIN, Role::MANAGEMENT]);
    }

    public function test_analytics_reports_rbac(): void
    {
        $this->assertReadAccess('/api/v1/admin/reports/summary', [
            Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE,
        ]);
    }

    public function test_unauthenticated_requests_are_rejected_on_every_admin_route(): void
    {
        foreach ([
            '/api/v1/admin/applications', '/api/v1/admin/artists', '/api/v1/admin/releases',
            '/api/v1/admin/rights-records', '/api/v1/admin/royalty-statements',
            '/api/v1/admin/licensing-requests', '/api/v1/admin/partners', '/api/v1/admin/events',
            '/api/v1/admin/products', '/api/v1/admin/news', '/api/v1/admin/users',
            '/api/v1/admin/roles', '/api/v1/admin/audit-log', '/api/v1/admin/reports/summary',
        ] as $uri) {
            $this->getJson($uri)->assertStatus(401);
        }
    }
}
