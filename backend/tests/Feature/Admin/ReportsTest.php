<?php

namespace Tests\Feature\Admin;

use App\Models\ArtistProfile;
use App\Models\Role;
use App\Models\RoyaltyStatement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_super_admin_gets_every_section(): void
    {
        Sanctum::actingAs($this->superAdmin());

        $response = $this->getJson('/api/v1/admin/reports/summary');

        $response->assertOk()->assertJsonPath('data.scope', 'full');
        foreach ([
            'artists', 'applications', 'releases', 'rights_records', 'events',
            'royalty', 'licensing_requests', 'partners', 'products', 'news', 'users',
        ] as $section) {
            $response->assertJsonStructure(['data' => [$section]]);
        }
    }

    public function test_ar_manager_only_sees_their_domain(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::AR_MANAGER));

        $response = $this->getJson('/api/v1/admin/reports/summary');

        $response->assertOk()->assertJsonPath('data.scope', 'artist_management');
        $response->assertJsonStructure(['data' => ['artists', 'applications', 'releases', 'rights_records', 'events']]);
        $response->assertJsonMissingPath('data.royalty');
        $response->assertJsonMissingPath('data.users');
        $response->assertJsonMissingPath('data.licensing_requests');
    }

    public function test_finance_only_sees_their_domain(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::FINANCE));

        $response = $this->getJson('/api/v1/admin/reports/summary');

        $response->assertOk()->assertJsonPath('data.scope', 'finance');
        $response->assertJsonStructure(['data' => ['royalty', 'licensing_requests']]);
        $response->assertJsonMissingPath('data.artists');
        $response->assertJsonMissingPath('data.users');
    }

    public function test_royalty_figures_sum_correctly(): void
    {
        $artist = ArtistProfile::factory()->create();
        RoyaltyStatement::factory()->create([
            'artist_profile_id' => $artist->id, 'total_revenue' => 1000, 'company_share' => 300, 'artist_share' => 700,
        ]);
        RoyaltyStatement::factory()->create([
            'artist_profile_id' => $artist->id, 'total_revenue' => 500, 'company_share' => 150, 'artist_share' => 350,
        ]);

        Sanctum::actingAs($this->superAdmin());
        $response = $this->getJson('/api/v1/admin/reports/summary');

        $response->assertJsonPath('data.royalty.total', 2)
            ->assertJsonPath('data.royalty.total_revenue', 1500)
            ->assertJsonPath('data.royalty.company_share', 450)
            ->assertJsonPath('data.royalty.artist_share', 1050);
    }

    public function test_content_manager_is_denied(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::CONTENT_MANAGER));

        $this->getJson('/api/v1/admin/reports/summary')->assertStatus(403);
    }
}
